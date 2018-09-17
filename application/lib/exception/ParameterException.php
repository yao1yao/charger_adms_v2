<?php

namespace app\lib\exception;

class ParameterException extends BaseException
{
    public $errMsg = '参数错误';
    public $respCode= 10001;
}