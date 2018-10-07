<?php
namespace app\api\controller\app_v1;


use app\common\model\UserInfo;
use app\common\model\UserRechargeRecord;
use EasyWeChat\Factory;
use think\Controller;
use think\Session;

class Wechat extends Controller
{

    private $app;
    public function _initialize()
    {
        $this->app = Factory::officialAccount(config('Wechat.config'));

    }

    /**
     * 用户点击自定义菜单入口
     */
    public function command($chargerNumber=0){
        $oauth =  $this->app ->oauth;
        if(empty(Session::get('wechat_user'))){
            Session::set('target_url',config('Host.domain').'app-entrance');
            $this->redirect($oauth->redirect()->getTargetUrl());
        }else{
            $user = Session::get('wechat_user');
            $userInfo = $this->app->user->get($user['original']['openid']);
            // 获取用户信息错误
            if(!empty($userInfo['errcode'])){
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/error');
            }
            // 用户未订阅，重定向到关注公众号界面
            if($userInfo['subscribe']===0){
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/focus');
            }
            // 如果不是扫描了电桩编号进入的
            if($chargerNumber===0){
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/login?openId='.$user['original']['openid']);
            }else{
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/charger-start?openId='.$user['original']['openid'].'&chargerNumber='.$chargerNumber);
            }
        }
    }
    public function oauthCallback(){
        $oauth =  $this->app ->oauth;
        $user = $oauth->user();
        Session::set('wechat_user',$user->toArray());
        $this->redirect(Session::get('target_url'),302);
    }

    public function createMenu(){
        $buttons = [
            [
                "type" => "view",
                "name" => "充电桩",
                "url"  => config('Host.domain').'app-entrance'
            ],
        ];
        $this->app ->menu->create($buttons);
    }
    public function getJssdk(){
        $jssdk = $this->app->jssdk;
        $jssdk->setUrl(config('Host.domain').'static/app_v1/index.html');
        return $jssdk->buildConfig(
            [
                'checkJsApi',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'translateVoice',
                'startRecord',
                'stopRecord',
                'onVoiceRecordEnd',
                'playVoice',
                'onVoicePlayEnd',
                'pauseVoice',
                'stopVoice',
                'uploadVoice',
                'downloadVoice',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView',
                'addCard',
                'chooseCard',
                'openCard'
            ], $debug = false, $beta = false, $json = true);
    }

    /**
     *  微信支付成功后的推送地址
     */
    function payPush(){
        // 获取推送数据
        $rawData = file_get_contents("php://input");
        // 推送记录日志
        file_put_contents(WECHAT_LOG_PATH.'wechat_pay.log',date("Y-m-d H:i:s").' —— '.$rawData.PHP_EOL,FILE_APPEND);
        $app = Factory::payment(config('Wechat.config'));
        $response = $app->handlePaidNotify(function($message,$fail){
            // 使用通知里的 "微信支付订单号" 或者 "商品订单号"去自己的数据库找到订单
            $orderNumber = $message['out_trade_no'];
            $rechargeRecordModel = new UserRechargeRecord();
            $userInfoModel = new UserInfo();
            $result = $rechargeRecordModel->where('out_trade_no',$orderNumber)->find();
            // 如果该订单不存在或者已经支付过了
            if(!$result||$result['recharge_status']===1){
                return true;
            }
            // 更新订单
            $updateOrder = [];
            // return_code 表示通信状态，不代表支付状态,这里表示微信通知成功
            if($message['return_code']==='SUCCESS'){
                // 微信支付成功了
                if($message['result_code']==='SUCCESS'){
                    $updateOrder=[
                        'recharge_status'=>1,
                        'recharge_money' => $message['cash_fee'],
                        'transaction_id' => $message['transaction_id']
                    ];
                }elseif($message['result_code']=== 'FAIL'){
                    // 表示用户支付成功，但是微信推送失败，该错误概率非常低
                    $updateOrder=[
                        'recharge_status'=>2,
                    ];
                }
            }else{
                return $fail('通信失败，请稍后再通知我');
            }
            // 更新订单
            $rechargeRecordModel->where('out_trade_no',$orderNumber)->update($updateOrder);
            // 更新账户余额
            $userInfo = $userInfoModel->where('id',$result['user_id'])->find();
            $updatePay = $userInfo['pay']+$message['cash_fee'];
            $userInfoModel->where('id',$result['user_id'])->update(['pay'=>$updatePay]);
            // 返回处理完成
            return true;
        });
        $response->send();
    }

}