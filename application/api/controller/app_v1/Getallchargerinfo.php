<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/10/8
 * Time: 上午10:10
 */

namespace app\api\controller\app_v1;


class Getallchargerinfo extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('allChargerInfo');
        // 获取所有在线信息
        $allInfo = $this->ChargerInfoModel->getAllChargerInfo();
        return chargerBack(100,$allInfo);
    }
}