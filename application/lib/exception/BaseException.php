<?php

namespace app\lib\exception;

use think\Exception;
use Throwable;

class BaseException extends Exception
{
    // HTTP 状态码
    public $code = 200;
    // 错误具体信息
    public $errMsg = "";
    // 自定义状态码
    public $respCode = null;
    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('errMsg',$params)){
            $this->errMsg = $params['errMsg'];
        }
        if(array_key_exists('respCode',$params)){
            $this->respCode = $params['respCode'];
        }
    }
}