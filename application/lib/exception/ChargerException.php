<?php

namespace app\lib\exception;

class ChargerException extends BaseException
{
    public $errMsg = '订单验证错误';
    public $respCode= 1000;
}