<?php
//设定订单的流水类型
//消费订单
define('CONSUME_RECORD',1);
//充值订单
define('RECHARGE_RECORD',2);
//发送请求方式
define('GET',0);
define('POST',1);

//返回前端数据格式
function respSuccess($data=[]){
    return json(array_merge($data,[
        'respCode'=>100
    ]));
}
//创建 token
function createToken($phone,$passsword)
{
    $token = md5($phone.$passsword);

    return $token;
}
function chargerBack($respCode,$message,$data=[]){
    return [
        'respCode'=>intval($respCode),
        'errMsg'=>$message,
        'data'=>$data,
    ];
}

