<?php
/**
 * 封装微信公众号的常用服务
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/10/7
 * Time: 下午10:35
 */

namespace app\api\service;



use EasyWeChat\Factory;

class Wechat
{
    // 充值事件
    const TEMPLATE_RECHARGE = 'recharge';
    // 充电事件
    const TEMPLATE_CONSUME = 'consume';
    // 提现成功通知
    const TEMPLATE_WITHDRAW = 'withdraw';

    public $app;

    public function __construct() {
        $this->app = Factory::officialAccount(config('Wechat.config'));
    }
    /**
     * 向用户发送模板消息
     * 参见 https://www.easywechat.com/docs/master/zh-CN/official-account/template_message
     * @param string $type 模板类型
     * @param array $template 模板内容
     * @param string $openId 用户的 openId
     */
    public function notifyTemplate($type,$template,$openId) {
        $templateId=config('Wechat.config')['template_id'];
        //todo::需要加上模板推送成功的判断
        switch($type){
            case Wechat::TEMPLATE_CONSUME:
                $result = $this->app->template_message->send([
                    'touser' => $openId,
                    'template_id' => $templateId['consume'], //模板 id
                    'url' => $template['url'], //目前不设置支付跳转页面
                    'data' => [
                        'first' => '您好,本次充电已结束',
                        'keyword1' => $template['consumeNumber'], //消费订单号
                        'keyword2' => $template['chargerNumber'], //电桩编号
                        'keyword3' => $template['startTime'],//开始充电时间
                        'keyword4' => $template['duration'].' 分钟',//充电时长,单位分钟
                        'keyword5' => number_format($template['fee'],2).' 元' , //充电费用,单位分
                        'remark' => '欢迎下次使用'
                    ],
                ]);
                return $result;
        }
    }
}