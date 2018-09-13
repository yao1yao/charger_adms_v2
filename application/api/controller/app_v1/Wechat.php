<?php
namespace app\api\controller\app_v1;


use EasyWeChat\Factory;
use think\Controller;
use think\Session;

class Wechat extends Controller
{

    function payPush(){
        $data = input('param.');
    }
    /**
     * 用户点击自定义菜单入口
     */
    public function command(){
        $app = Factory::officialAccount(config('Wechat.config'));
        $oauth = $app->oauth;
        if(empty(Session::get('wechat_user'))){
            Session::set('target_url',config('Host.domain').'app-entrance');
            $this->redirect($oauth->redirect()->getTargetUrl());
        }else{
            $user = Session::get('wechat_user');
            $this->redirect(config('Host.domain').'static/app_v1/index.html#?openId=' . $user['original']['openid']);
        }
    }
    public function oauthCallback(){
        $app = Factory::officialAccount(config('Wechat.config'));
        $oauth = $app->oauth;
        $user = $oauth->user();
        Session::set('wechat_user',$user->toArray());
        $this->redirect(Session::get('target_url'),302);
    }

    public function createMenu(){
        $app = Factory::officialAccount(config('Wechat.config'));
        $buttons = [
            [
                "type" => "view",
                "name" => "充电桩",
                "url"  => config('Host.domain').'app-entrance'
            ],
        ];
        $app->menu->create($buttons);
    }
}