<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 上午10:58
 */

namespace app\api\service;


class VerfCode
{
    /**
     * @param int $len
     * @return string
     * 默认生成六位的随机数
     */
    public static function GetRandStr($len=6){
        $chars = array(
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen=count($chars)-1;
        shuffle($chars);//将数组元素按随机数顺序重新排序
        $output="";
        for($i=0;$i<$len;$i++){
            $output.=$chars[mt_rand(0,$charsLen)];
        }
        return $output;
    }
}