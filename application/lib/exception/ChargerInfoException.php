<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 下午5:25
 */

namespace app\lib\exception;


class ChargerInfoException extends BaseException
{
    public $errMsg = 'charger error';
    public $respCode = 60001;
}