<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/20
 * Time: 下午12:20
 */

namespace app\lib\exception;


class NotFoundException extends BaseException
{
    public $code = 200;
    public $errMsg = 'not found';
    public $respCode= 404;
}