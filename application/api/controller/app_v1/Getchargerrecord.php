<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/29
 * Time: 下午2:08
 */

namespace app\api\controller\app_v1;


class Getchargerrecord extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('getChargerRecord');
        $chargerRecord = $this->UserChargingRecordModel->getChargerRecord($data['userId']);
        return chargerBack(100,$chargerRecord);
    }
}