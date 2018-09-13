<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/13
 * Time: 上午11:37
 */

namespace app\lib\exception;


class SqlException extends BaseException
{
    public $code = 200;
    public $errMsg = '服务器内部错误';
    public $respCode= 30001;
}