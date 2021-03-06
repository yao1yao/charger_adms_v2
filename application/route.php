<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// app 接口路由
Route::post('v1/orders','api/app_v1.createorder/command');
Route::post('v1/login','api/app_v1.login/command');
Route::post('v1/register','api/app_v1.register/command');
Route::post('v1/forgetpwd','api/app_v1.forgetpwd/command');
Route::get('v1/verfcode','api/app_v1.getverfcode/command');
Route::get('v1/charger-info','api/app_v1.updatechargerinfo/command');
Route::post('v1/charger-start','api/app_v1.startcharging/command');
Route::post('v1/charger-end','api/app_v1.endcharging/command');
Route::post('v1/charging-info','api/app_v1.getcharginginfo/command');
Route::get('v1/charger-record','api/app_v1.getchargerrecord/command');
Route::get('v1/recharger-record','api/app_v1.getrechargerrecord/command');
Route::post('v1/feedback','api/app_v1.feedback/command');
Route::get('v1/newest-balance','api/app_v1.getnewestbalance/command');
Route::post('v1/user-datum','api/app_v1.modifydatum/command');
Route::post('v1/logout','api/app_v1.logout/command');
Route::post('v1/notify','api/app_v1.devicenotify/command');
Route::post('v1/withdraw','api/app_v1.withdraw/command');
Route::get('v1/allchargerinfo','api/app_v1.getallchargerinfo/command');

// 微信路由
Route::any('wechat/pay-push','api/app_v1.wechat/paypush');
Route::any('wechat/oauth-callback','api/app_v1.wechat/oauthcallback');
Route::any('app-entrance','api/app_v1.wechat/command');
Route::any('menus','api/app_v1.wechat/createmenu');
Route::get('wechat/jssdk','api/app_v1.wechat/getjssdk');

// adms 接口路由
Route::post('adv1/notify','api/adms_v1.devicenotify/command');
