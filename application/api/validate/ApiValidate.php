<?php

namespace app\api\validate;

class ApiValidate extends BaseValidate{
    protected $rule = [
        ['openId','require|isNotEmpty'],
        ['rechargeMoney','require|isNotEmpty'],
        ['userId','require|isPositiveInteger'],
        ['password','require|isNotEmpty'],
        ['phone','require|isMobile'],
        ['msgId','require|isMsgId'],
        ['userName','require|isNotEmpty'],
        ['verfCode','require|isNotEmpty'],
        ['chargerNumber','require'],
        ['type','require'],
        ['value','require'],
        ['payType','require'],
    ];
    protected $scene=[
        'createOrder'=>['openId','rechargeMoney','userId','msgId'],
        'login'=>['phone','password','msgId'],
        'getVerfCode'=>['phone','msgId'],
        'register'=>['phone','userName','openId','password','verfCode','msgId'],
        'forgetPwd'=>['phone','password','verfCode','msgId'],
        'updateChargerInfo'=>['chargerNumber','msgId'],
        'startCharging'=>['msgId','openId','type','userId','chargerNumber','value','payType'],
        'getChargingInfo'=>['msgId','chargerNumber','userId'],
        'endCharging'=>['msgId','chargerNumber','userId'],
        'getChargerRecord'=>['msgId','userId']
    ];
}