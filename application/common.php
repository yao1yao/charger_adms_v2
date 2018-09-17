<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 通用的分页样式
 * @param $obj
 * @return string
 */
function pagniation($obj)
{

    if (!$obj) {
        return '';
    }
    //优化方案
    $params = request()->param();
    return '<div class="cl pd-5 bg-1 bk-gray  tp5-charger">' . $obj->appends($params)->render() . '</div>';

}

function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;
    for($i=0;$i<$length;$i++)
    {
        $str.=$strPol[rand(0,$max)];
    }
    return $str;
}

