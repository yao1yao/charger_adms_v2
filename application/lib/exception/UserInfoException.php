<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/17
 * Time: 下午4:09
 */

namespace app\lib\exception;


class UserInfoException extends BaseException
{
    public $errMsg = 'user error';
    public $respCode = 40001;
}