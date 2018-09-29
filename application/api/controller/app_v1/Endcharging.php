<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/29
 * Time: 上午10:58
 */

namespace app\api\controller\app_v1;


class Endcharging extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('endCharging');
        // 获取设备 ID
        $deviceId = $this->ChargerInfoModel->getDeviceId($data['chargerNumber']);
        // 结束设备充电
        $chargingInfo = $this->ChargerInfoModel->endCharging($deviceId);
        // 如果还在充电中，需要继续更新记录
        if($chargingInfo['isCharging']){
            $this->UserChargingRecordModel->updateChargingRecord($data['userId'],$chargingInfo['energy'],$chargingInfo['duration']);
        }
        // 结束成功更新用户状态
        $this->UserInfoModel->isUpdate(true)->save(['is_charging'=>0], ['id' => $data['userId']]);

        return chargerBack(100,["message"=>"结束成功"]);
    }
}