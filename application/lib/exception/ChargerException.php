<?php

namespace app\lib\exception;

class ChargerException extends BaseException
{
    public $errMsg = '充电桩类型错误';
    public $respCode= 60000;
}