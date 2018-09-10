<?php

namespace app\api\validate;

class UserInfoValidate extends BaseValidate{
    protected $rule = [
        ['openId','require|isNotEmpty'],
        ['rechargeMoney','require|isNotEmpty'],
        ['userId','require|isPositiveInteger'],
    ];
    protected $scene=[
        'createOrder'=>['openId','rechargeMoney','userId'],
    ];
}