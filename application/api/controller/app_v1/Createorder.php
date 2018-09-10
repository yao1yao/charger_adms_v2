<?php

namespace app\api\controller\app_v1;

use app\api\validate\UserInfoValidate;
use app\common\model\UserRechargeRecord;
use think\Cache;
use think\Controller;

class Createorder extends Controller{
    public function command(UserInfoValidate $validate)
    {
        $data = $validate->goCheck('createOrder');
        $jssdkOrder = UserRechargeRecord::createOrder();
        return 123;
    }
}