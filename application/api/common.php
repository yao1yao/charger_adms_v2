<?php
//设定订单的流水类型
//消费订单
define('CONSUME_RECORD',1);
//充值订单
define('RECHARGE_RECORD',2);
//发送请求方式
define('GET',0);
define('POST',1);

// 充电类型
define('CHARGING_TYPE_MONEY',0);
define('CHARGING_TYPE_TIME',1);

// 充电支付方式
define('PAY_TYPE_WECHAT',1);
define('PAY_TYPE_BALANCE',2);

//返回前端数据格式
function respSuccess($data=[]){
    return json(array_merge($data,[
        'respCode'=>100
    ]));
}

// api 返回返回
function chargerBack($respCode,$data=[]){
    return json([
        'respCode'=>intval($respCode),
        'data'=>$data,
    ]);
}

//构造唯一订单
/**
 * 生成提现订单号
 * 订单号 = 月日时分秒(10)+随机码(4)+88
 * @param int $type 执行随机订单号类型
 * @return string 返回订单号
 */
function generateOrderNumber($type=88){
    return date("mdHis") . sprintf("%04d", mt_rand(0, 9999)) . sprintf("%02d", $type);
}

