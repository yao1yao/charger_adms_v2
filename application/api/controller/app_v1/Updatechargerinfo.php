<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/20
 * Time: 上午11:46
 */

namespace app\api\controller\app_v1;


class Updatechargerinfo extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('updateChargerInfo');
        // 检查设备是否已注册
        $result = $this->ChargerInfoModel->getChargerInfo($data['chargerNumber']);
        return chargerBack(100,$result);
    }
}