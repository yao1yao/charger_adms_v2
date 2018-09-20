<?php

namespace app\api\controller\app_v1;

use app\api\validate\ApiValidate;
use app\common\model\UserRechargeRecord;
use think\Cache;
use think\Controller;

class Createorder extends BaseController
{
    public function command()
    {
        $data = $this->ApiValidate->goCheck('createOrder');
        $jssdkOrder = $this->UserRechargeRecordModel->createOrder($data);
        return respSuccess($jssdkOrder);
    }
}