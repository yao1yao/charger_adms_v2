<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 上午10:48
 */

namespace app\lib\exception;


class VerfCodeException extends BaseException
{
    public $code = 200;
    public $errMsg = '验证码错误';
    public $respCode= 50001;
}