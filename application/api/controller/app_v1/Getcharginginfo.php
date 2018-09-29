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
        $chargingInfo = $this->ChargerInfoModel->getChargingInfo($deviceId);

        // 如果还在充电，更新当前设备的充电时间和耗电量
        if($chargingInfo['isCharging']){
            $this->UserChargingRecord->updateChargingRecord($data['userId'],$chargingInfo['energy'],$chargingInfo['duration']);
        }
        return chargerBack(100,$chargingInfo);

    }
}