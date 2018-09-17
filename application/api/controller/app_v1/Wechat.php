<?php
namespace app\api\controller\app_v1;


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
    function payPush(){
        $data = input('param.');
    }
    /**
     * 用户点击自定义菜单入口
     */
    public function command(){
        $oauth =  $this->app ->oauth;
        if(empty(Session::get('wechat_user'))){
            Session::set('target_url',config('Host.domain').'app-entrance');
            $this->redirect($oauth->redirect()->getTargetUrl());
        }else{
            $user = Session::get('wechat_user');
            $this->redirect(config('Host.domain').'static/app_v1/index.html#?openId=' . $user['original']['openid']);
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
            ], $debug = true, $beta = false, $json = true);
    }
}