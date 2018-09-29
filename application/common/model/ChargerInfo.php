<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/20
 * Time: 下午1:55
 */

namespace app\common\model;


use app\lib\exception\ChargerException;
use app\lib\exception\ChargerInfoException;
use app\lib\exception\NotFoundException;
use think\Model;

class ChargerInfo extends Model
{
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function deviceInfo(){
        return $this->belongsTo('DeviceInfo','device_id','id')->field('device_power');
    }
    public function ChargerBill(){
        return $this->hasOne('ChargerBillModel','id','cost_id')->field('energy_rate,service_rate,min_pay');
    }

    /**
     * 获取当前设备的功率，电费，服务费
     * @param $chargerNumber
     * @return array
     * @throws ChargerException
     * @throws NotFoundException
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChargerInfo($chargerNumber){
        $data=[
            'status'=>1,
            'charger_number'=>intval($chargerNumber),
        ];
        $chargerInfo = $this->where($data)->find();
        if(!$chargerInfo){
            throw new NotFoundException([
                'errMsg'=>'设备不存在'
            ]);
        }
        // 检查设备当前是否可用
        $this->checkChargerIsFree($chargerInfo['device_id']);
        $chargerDeviceInfo = self::get(['device_id'=>$chargerInfo['device_id']]);
        $chargerBillModelInfo = self::get(['cost_id'=>$chargerInfo['cost_id']]);
        $data=[
            "devicePower"=>$chargerDeviceInfo->deviceinfo->device_power,
            "energyRate"=>$chargerBillModelInfo->chargerbill->energy_rate,
            "serviceRate"=>$chargerBillModelInfo->chargerbill->service_rate
        ];
        // 关联获取数据
        return $data;
    }

    /**
     * 判断当前设备是否可用
     * @param $deviceId
     * @throws ChargerException
     * @throws \Exception
     */
    public function checkChargerIsFree($deviceId){
        $url = config('DevServer.ServerUrl') . config('DevServer.ServerApiName')['getChargerStatus'];
        $data = [
            'deviceId' => intval($deviceId),
            'msgId' => config('DevServer.msgId')
        ];
        $result = sendCommand($url, POST, $data);
        if (!$result) {
            throw new \Exception('内部错误');
        }
        if ($result['data']['respCode'] !== 100) {
            throw new ChargerException([
                'errMsg' => '当前设备暂不可用'
            ]);
        }
        if ($result['data']['connect'] === 0) {
            throw new ChargerException([
                'errMsg' => '充电枪未连接'
            ]);
        }
        switch($result['data']['status']){
            case 0:
                throw new ChargerException([
                    'errMsg' => '充电桩已离线'
                ]);
                break;
            case 2:
                throw new ChargerException([
                    'errMsg' => '充电桩正在充电中'
                ]);
                break;
            case 3:
                throw new ChargerException([
                    'errMsg' => '充电桩已预约'
                ]);
                break;
            case 16400:
                throw new ChargerException([
                    'errMsg' => '电表异常'
                ]);
                break;
        }
    }


    /**
     * 转化为需要花费的金额,并判断设备是否可用
     * 1.设备是否可用
     *
     * @param $chargerNumber
     * @throws ChargerException
     * @throws NotFoundException
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function estimateMoney($chargerNumber,$type,$value){
        $currentMoney = 0;
        $result=$this->getChargerInfo($chargerNumber);
        switch($type)
        {
            case CHARGING_TYPE_MONEY:
                $currentMoney = $value;
                break;
            case CHARGING_TYPE_TIME:
                $currentMoney = ($result['energyRate']+$result['serviceRate'])*$result['devicePower']*$value/60;
        }
        return $currentMoney;
    }
    public function startCharging($userId,$chargerNumber,$type,$value){
        // 转化为设备Id
        $data=[
            'status'=>1,
            'charger_number'=>intval($chargerNumber),
        ];
        $chargerInfo = $this->where($data)->find();
        // 将用户输入转化为充电信息
        $chargingInfo = $this->convertType($chargerNumber,$type,$value);
        // 构造发送到连接层接口的所需字段
        $url = config('DevServer.ServerUrl') . config('DevServer.ServerApiName')['setChargerStart'];
        $data = [
            'deviceId' => intval($chargerInfo['device_id']),
            'msgId' => config('DevServer.msgId'),
            'userId'=>intval($userId),
            'type'=>intval($type),
            'time'=>intval($chargingInfo['time']),
            'energy'=>intval($chargingInfo['energy'])
        ];
        $result = sendCommand($url, POST, $data);
        if($result['data']['respCode']!==100){
            throw new ChargerInfoException([
                'respCode'=>60002,
                'errMsg'=> '设备开启失败'
            ]);
        }
        return $data;
    }
    public function convertType($chargerNumber,$type,$value){
        $result=$this->getChargerInfo($chargerNumber);
        switch($type){
            case CHARGING_TYPE_MONEY:
                return [
                    'time'=>0,
                    'energy'=>($value)/($result['energyRate']+$result['serviceRate']),
                ];
                break;
            case CHARGING_TYPE_TIME:
                return [
                    'time'=>$value,
                    'energy'=>0
                ];
        }
    }

    /**
     * 根据设备编号获取设备id
     * @param $chargerNumber
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDeviceId($chargerNumber){
        $data=[
            'status'=>1,
            'charger_number'=>intval($chargerNumber),
        ];
        $chargerInfo = $this->where($data)->find();
        return $chargerInfo['device_id'];
    }

    /**
     * 获取正在充电的设备充电信息
     * @param $deviceId
     * @return array
     * @throws ChargerInfoException
     */
    public function getChargingInfo($deviceId){

        $url = config('DevServer.ServerUrl') . config('DevServer.ServerApiName')['getChargingInfo'];
        $data = [
            'deviceId' => intval($deviceId),
            'msgId' => config('DevServer.msgId')
        ];
        $result = sendCommand($url, POST, $data);
        if($result['data']['respCode']!==100){
            throw new ChargerInfoException([
                'errMsg'=>'设备未在充电中'
            ]);
        }
        return [
            'isCharging' => $result['data']['status']===2? true:false,
            'energy' => $result['data']['energy'],
            'voltage' => $result['data']['voltage'],
            'current' => $result['data']['current'],
            'power' => $result['data']['power'],
            'duration'=>$result['data']['duration']
        ];
    }
    public function endCharging($deviceId){
        $url = config('DevServer.ServerUrl') . config('DevServer.ServerApiName')['setChargerEnd'];
        $data = [
            'deviceId' => intval($deviceId),
            'msgId' => config('DevServer.msgId')
        ];
        $result = sendCommand($url, POST, $data);
        if($result['data']['respCode']!==100){
            throw new ChargerInfoException([
                'errMsg'=>'设备结束充电失败'
            ]);
        }
        return [
            'isCharging' => $result['data']['status']===2? true:false,
            'energy' => $result['data']['energy'],
            'duration'=>$result['data']['duration']
        ];
    }
}


