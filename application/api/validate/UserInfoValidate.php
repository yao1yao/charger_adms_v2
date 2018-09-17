<?php

namespace app\api\validate;

class UserInfoValidate extends BaseValidate{
    protected $rule = [
        ['openId','require|isNotEmpty'],
        ['rechargeMoney','require|isNotEmpty'],
        ['userId','require|isPositiveInteger'],
        ['password','require|isPositiveInteger'],
        ['phone','require|isMobile'],
        ['msgId','require|isMsgId'],
    ];
    protected $scene=[
        'createOrder'=>['openId','rechargeMoney','userId','msgId'],
        'login'=>['phone','password','msgId']
    ];
}