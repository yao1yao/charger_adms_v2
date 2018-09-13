<?php
/**
 * 微信配置文件
 */

return [
    "wechat_receive_access_token_url" => "https://api.weixin.qq.com/cgi-bin/token",
    "wechat_create_menu_url" => "https://api.weixin.qq.com/cgi-bin/menu/create",
    "wechat_custom_send_url" => "https://api.weixin.qq.com/cgi-bin/message/custom/send",
    "openid" => "oBQUC1hPc8LBOrvY8BBpn5m_HmJY",
    "jsonmenu" => [
            "button"=>[
                [
                  "type"=>"view",
                  "name"=>"充电桩",
                  "url"=>"http://alfredyao.applinzi.com/api/v1.wechat/command"
                ],
            ]
        ],
    "config" =>[
        'app_id' => 'wx8b7da449f409637b',
        'secret' => 'ea5b1ef1996f176e6c332f195127027f',
        'token'   => 'wechat',

        //指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        //订单配置
        //微信支付配置
        'mch_id' => '1482329052',
        'key' => 'e3b0c44298fc1c149afbf4c8996fb924',
        'cert_path' => dirname(__DIR__) . '/cert/apiclient_cert.pem',
        'key_path' => dirname(__DIR__) . '/cert/apiclient_key.pem',
        'notify_url'=> 'http://charger.natapp1.cc/wechat/pay-push',
        'oauth' => [
            'scopes'   => ['snsapi_base'],
            'callback' => "wechat/oauth-callback",
        ],
        //模板 id 配置,该配置不属于 easywechat 但可以放入其中
        'template_id' => [
            //登录微信公众平台->模板消息->会员充值成功通知
            'recharge' => 'csWJ8bIyiw5exLsQTcmsDSVmuSaPZcE6gEaVpy63x9w',
            //登录微信公众平台->模板消息->充电结束提醒
            'consume' => 'S0fBTbHTaQphcZsP2jaDChuEJUuaRRqz6z2-sdmJy5w',
            //登录微信公众平台->模板消息->提现成功通知
            'withdraw' => 'b5twdNm2TH7-fL6_gMO2YwDj7hGCf72K0_xHSQ4a4NE',
        ],

        'response_type' => 'array',
    ],
];
