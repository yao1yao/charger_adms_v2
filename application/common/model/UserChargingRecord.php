<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 下午5:53
 */

namespace app\common\model;


use app\lib\exception\NotFoundException;
use app\lib\exception\SqlException;
use think\Cache;
use think\Model;

class UserChargingRecord extends Model
{
    protected $createTime = 'start_time';
    protected $updateTime = 'end_time';
    protected $autoWriteTimestamp = "datetime";

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
     * 从之前的缓存中找到唯一的充电但单号 consumeNumber
     * @param $userId 用户 ID
     */
    public function updateChargingRecord($userId,$energy,$duration)
    {
        $billInfo = Cache::get($userId);
        if(!$billInfo){
            throw new NotFoundException([
                'errMsg'=>'未找到此订单'
            ]);
        }
        $this->where('consume_number',$billInfo['consume_number'])->update([
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
                'errMsg'=>'暂无充电记录',
                'respCode'=>30002
            ]);
        }
        // 改用户总共充电的时间
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
}