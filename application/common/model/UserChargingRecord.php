<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 下午5:53
 */

namespace app\common\model;


use app\api\service\Wechat;
use app\lib\exception\ChargerInfoException;
use app\lib\exception\NotFoundException;
use app\lib\exception\SqlException;
use EasyWeChat\Factory;
use think\Cache;
use think\Model;

class UserChargingRecord extends Model
{
    protected $createTime = 'start_time';
    protected $updateTime = 'end_time';
    protected $autoWriteTimestamp = "datetime";
    private $wechatServer;
    public function _initialize()
    {
        $this->wechatServer=new Wechat();
    }

    /**
     * 根据 userId 生成缓存
     * @param $userId   用户 ID
     * @param $data     存储的数据
     * @return bool
     * @throws SqlException
     */
    public function saveChargingRecord($userId,$data){
        $result = $this->save($data);
        if(!$result){
            throw new SqlException();
        }
        return Cache::set($userId,$data);
    }

    /**
     * 从之前的缓存中找到唯一的充电单号 consumeNumber
     * 更新用户充电记录
     * @param $userId 用户 ID
     */
    public function updateChargingRecord($userId,$energy,$duration)
    {
        $consumeNumber=$this->getChargingCacheInfo($userId)['consume_number'];
        $this->where('consume_number',$consumeNumber)->update([
            'duration'=>$duration,
            'energy'=>$energy
        ]);
    }
    public function getChargerRecord($userId){
        $chargerRecord = $this->where(['user_id'=>$userId])
            ->whereTime('start_time','month')
            ->field('user_id,set_duration,set_energy,end_type,charging_type,consume_number',true)
            ->order('id','desc')
            ->select();
        if(!$chargerRecord){
            throw new SqlException([
                'errMsg'=>'本月暂无新的充电记录',
                'respCode'=>30002
            ]);
        }
        // 该用户总共充电的时间
        $sumDuration = $this->where(['user_id'=>$userId])->sum('duration');
        // 用户总共充电的度数
        $sumEnergy = $this->where(['user_id'=>$userId])->sum('energy');
        // 生成带地址的充电记录
        $record=[];
        foreach ($chargerRecord as $key=>$value){
            $address = model('ChargerInfo')->where('charger_number',intval($value['charger_number']))->column('address');
            $arr = json_decode(json_encode($value),true);
            $chargerAddress=[
                "address"=>$address[0]
            ];
            $record[$key]= array_merge($arr,$chargerAddress);

        }
        return [
            'allRecord'=>$record,
            'sumDuration'=>$sumDuration,
            'sumEnergy'=>$sumEnergy
        ];
    }
    // 充电结束后，进行结算
    public function settleCharging($deviceId,$userId,$energy,$endType)
    {
        // 获取缓存中的信息
        $consumeInfo= $this->getChargingCacheInfo($userId);
        $chargingRecord = $this->where(['consume_number'=>$consumeInfo['consume_number']])->find();
        if(!$chargingRecord){
            throw new NotFoundException([
                'errMsg'=>'订单不存在'
            ]);
        }
        // 如果该记录已经结算过
        if($chargingRecord['record_status']){
            throw new ChargerInfoException([
                'errMsg'=>'订单已结算'
            ]);
        }
        // 调用充电信息模型计算所需花费
        $consumeMoney = model('ChargerInfo')->calculateConsume($deviceId,$energy);
        // 更新记录
        $this->where('consume_number',$consumeInfo['consume_number'])->update([
            'energy'=>$energy,
            'energy_money'=>$consumeMoney['energyMoney'],
            'service_money'=>$consumeMoney['serviceMoney'],
            'end_type'=>$endType,
            'record_status'=>1,
            'end_time'=> date('Y-m-d H:i:s')
        ]);
        // 调用 UserInfo 模型同步更新账户余额,更新用户当前状态为未充电
        $pay = UserInfo::where('id',$userId)->value('pay');
        model('UserInfo')->where('id',$userId)->update([
            'pay'=>$pay-($consumeMoney['energyMoney']+$consumeMoney['serviceMoney']),
            'is_charging'=>0
        ]);
        // 向用户推送
        $openId = UserInfo::where('id',$userId)->value('open_id');
        $result = (new Wechat())->notifyTemplate(Wechat::TEMPLATE_CONSUME,[
            'url'=>config('Host.domain').'static/app_v1/index.html#/charger/record',
            'consumeNumber'=>$chargingRecord['consume_number'],
            'chargerNumber'=>$chargingRecord['charger_number'],
            'startTime'=>$chargingRecord['start_time'],
            'duration'=>$chargingRecord['duration'],
            'fee'=>$consumeMoney['energyMoney']+$consumeMoney['serviceMoney']
        ],$openId);
        // 推送成功删除缓存
        if($result['errcode']===0){
            Cache::rm($userId);
        }
    }
    /**
     * 获取缓存信息
     */
    private function getChargingCacheInfo($userId) {
        $cacheInfo = Cache::get($userId);
        if(!$cacheInfo) {
            throw new NotFoundException([
                'errMsg'=>'充电订单缓存已失效'
            ]);
        } else {
            return  $cacheInfo;
        }
    }

    /**
     * 向用户推送充电记录
     * @param $consumeInfo  mixed
     * @param $consumeMoney float 充电总金额
     * @param $openId string 用户 openId
     */
    public function pushMessage($openId,$consumeInfo,$consumeMoney){
       return  (new Wechat())->notifyTemplate(Wechat::TEMPLATE_CONSUME,[
            'url'=>config('Host.domain').'static/app_v1/index.html#/charger/record',
            'consumeNumber'=>$consumeInfo['consume_number'],
            'chargerNumber'=>$consumeInfo['charger_number'],
            'startTime'=>$consumeInfo['start_time'],
            'duration'=>$consumeInfo['duration'],
            'fee'=>$consumeMoney
        ],$openId);
    }
}