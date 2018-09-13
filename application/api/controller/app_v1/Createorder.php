<?php

namespace app\api\controller\app_v1;

use app\api\validate\UserInfoValidate;
use app\common\model\UserRechargeRecord;
use think\Cache;
use think\Controller;

class Createorder extends Controller{
    private $UserRecrgeRecordModel;
    public function _initialize()
    {
        $this->UserRecrgeRecordModel=model('UserRechargeRecord');
    }
    public function command(UserInfoValidate $validate)
    {
        $data = $validate->goCheck('createOrder');
        $jssdkOrder = $this->UserRecrgeRecordModel->createOrder($data);
        return respSuccess($jssdkOrder);
    }
}