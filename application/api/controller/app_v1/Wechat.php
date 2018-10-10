<?php
namespace app\api\controller\app_v1;


use app\common\model\UserInfo;
use app\common\model\UserRechargeRecord;
use EasyWeChat\Factory;
use think\Cache;
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
            $openId = $user['original']['openid'];
            $userInfo = $this->app->user->get($openId);
            // 获取用户信息错误
            if(!empty($userInfo['errcode'])){
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/error');
            }
            // 用户未订阅，重定向到关注公众号界面
            if($userInfo['subscribe']===0){
                $this->redirect(config('Host.domain').'static/app_v1/index.html#/focus');
            }
            // 如果不是扫描了电桩编号进入的
            // 获取用户注册过的信息
            $result = UserInfo::where(['open_id'=>$openId])->find();
            // 如果用户已注册
            if($result){
                // 如果用户通过公众号自定义菜单方式进入
                if($chargerNumber===0){
                    // 如果用户已注册过，但是当前状态为未登录
                    if($result['is_login']===0) {
                        $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/login');
                    // 如果用户已注册过，但是当前状态为登录
                    }else{
                        // 如果用户未在充电中
                        if($result['is_charging']===0){
                            $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/home');
                        // 如果用户在充电中
                        }else{
                            // 获取缓存的信息
                            $cacheInfo = Cache::get($result['id']);
                            $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/charger?'
                                .'chargerNumber='.$cacheInfo['charger_number']
                                .'&chargingType='.$cacheInfo['charging_type']
                                .'&setDuration='.$cacheInfo['set_duration']
                                .'&setEnergy='.$cacheInfo['set_energy']
                            );
                        }
                    }
                 // 如果用户是通过扫码进入的
                }else{
                    // 如果用户未登录
                    if($result['is_login']===0){
                        $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/login');
                    // 如果用户已登录
                    }else{
                        // 如果用户未在充电中
                        if($result['is_charging']===0){
                            $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/charger-start?chargerNumber='.$chargerNumber);
                        // 如果用户正在充电中
                        }else{
                            $cacheInfo = Cache::get($result['id']);
                            $this->redirect(config('Host.domain') . 'static/app_v1/index.html#/charger?'
                                .'chargerNumber='.$cacheInfo['charger_number']
                                .'&chargingType='.$cacheInfo['charging_type']
                                .'&setDuration='.$cacheInfo['set_duration']
                                .'&setEnergy='.$cacheInfo['set_energy']
                            );
                        }
                    }
                }
            }else{
              // 如果用户未注册
              $this->redirect(config('Host.domain').'static/app_v1/index.html#/login?openId='.$openId);
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