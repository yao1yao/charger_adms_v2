<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/20
 * Time: 下午1:55
 */

namespace app\common\model;


use app\lib\exception\ChargerException;
use app\lib\exception\NotFoundException;
use think\Model;

class ChargerInfo extends Model
{
    protected $autoWriteTimestamp = "datetime";
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function deviceInfo(){
        return $this->belongsTo('DeviceInfo','device_id')->field('device_power');
    }
    public function ChargerBill(){
        return $this->hasOne('ChargerBillModel','id','cost_id')->field('energy_rate,service_rate,min_pay');
    }
    public function getChargerInfo($deviceId){
        $data=[
            'status'=>1,
            'device_number'=>intval($deviceId),
        ];
        $Chargerinfo = $this->where($data)->find();
        if(!$Chargerinfo){
            throw new NotFoundException([
                'errMsg'=>'设备不存在'
            ]);
        }
        // 获取设备当前状态
        $this->checkChargerIsFree($Chargerinfo['device_id']);
        $res = $this->with('deviceInfo')->find($Chargerinfo['device_id']);
        return $res;
        // 关联获取数据
        return $Chargerinfo;
    }
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
}