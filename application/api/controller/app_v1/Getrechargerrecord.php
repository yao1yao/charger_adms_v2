<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/29
 * Time: 下午3:56
 */

namespace app\api\controller\app_v1;


class Getrechargerrecord extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('getReChargerRecord');
        $reChargerRecord = $this->UserRechargeRecordModel->getReChargerRecord($data['userId']);
        return chargerBack(100,$reChargerRecord);
    }
}