<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/28
 * Time: 下午3:42
 */

namespace app\api\controller\app_v1;


class Getcharginginfo extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('getChargingInfo');
        // 获取设备id
        $deviceId = $this->ChargerInfoModel->getDeviceId($data['chargerNumber']);
        // 获取开始充电信息
        $chargingInfo = $this->ChargerInfoModel->getChargingInfo($data['userId'],$deviceId);
        // 如果在充电，更新当前设备的充电时间和耗电量充电记录
        if($chargingInfo['isCharging']){
            $consumeNumber = $this->UserChargingRecordModel->getChargingCacheInfo($data['userId']);
            $updateRecord=[
                'energy'=>$chargingInfo['energy'],
                'duration'=>$chargingInfo['duration']
            ];
            $this->UserChargingRecordModel->updateChargingRecord($consumeNumber['consume_number'],$updateRecord);
        }
        return chargerBack(100,$chargingInfo);
    }
}