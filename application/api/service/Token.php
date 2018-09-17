<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/17
 * Time: 下午4:38
 */

namespace app\api\service;


use think\cache\driver\Memcached;

class Token
{
    public static function generateToken()
    {
        //32个字符组成的一组随机字符串
        $randChars = getRandChar(32);
        //用三组字符串，进行 MD5 加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐 加密需要用到的随机字符串
        $salt = "charger";
        return md5($randChars . $timestamp . $salt);
    }
    // 判断 token 是否存在
    public static function verifyToken($token,Memcached $mmc)
    {
        $exist = $mmc->get($token);
        if($exist){
            return true;
        }else{
            return false;
        }
    }
}