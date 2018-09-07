<?php
/**
 *  用来生成不重复的序列号
 */
namespace RandString;
class Serial{

    /**
     * @param int $len
     * @return string
     * 默认生成十位的随机数
     */
    public static function GetRandStr($len=10){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen=count($chars)-1;
        shuffle($chars);//将数组元素按随机数顺序重新排序
        $output="";
        for($i=0;$i<$len;$i++){
            $output.=$chars[mt_rand(0,$charsLen)];
        }
        return $output;
    }

    /**
     * @param $arr
     * @return string
     * 生成不在$arr中的序列号
     */
    public static function getUniqueSerial($arr,$len){
        $str=self::GetRandStr($len);
        if(in_array($str,$arr)){
            $str=self::getUniqueSerial($arr,$len);
        }
        return $str;
    }

}

