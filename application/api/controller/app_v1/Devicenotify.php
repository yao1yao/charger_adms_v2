<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/10/7
 * Time: 下午4:27
 */

namespace app\api\controller\app_v1;

class Devicenotify extends BaseController
{

    public function command(){
        $info = input('param.');
        // 只处理设备推送的结束充电
        if ($info['apiName'] != 'notifyEndCharging') {
            return "Incorrect apiName";
        }
        // 结束充电后，计算所需花费，更新充电记录与账户余额, 推送信息给用户
        $this->UserChargingRecordModel->settleCharging($info['deviceId'],$info['userId'],$info['energy'],$info['duration'],$info['endType']);
        return json(['repsCode'=>100]);
    }
}