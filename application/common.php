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


/**
 * 构造向连接层发送数据方法
 * @param $url
 * @param int $type
 * @param $data
 * @return mixed
 *
 */
function sendCommand($url,$type = 0,$data){
    $sendData = json_encode($data);
    $result = doCurl($url,$type,$sendData);
    return json_decode($result,true);
}

//封装 doCurl
/**
 * @param $url
 * @param int $type 0 get 1 post
 * @param string $data
 */
function doCurl($url, $type = 0, $data)
{
    $ch = curl_init();//初始化

    //设置选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        )
    );

    if ($type == 1) {
        //post
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    //执行并获取内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $output;
}

/**
 * @param $url
 * @param int $type 0 get 1 post
 * @param array $data
 */
