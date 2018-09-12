<?php

namespace app\lib\exception;

class WechatException extends BaseException
{
    public $code = 200;
    public $errMsg = '微信错误';
    public $respCode= 20001;
}