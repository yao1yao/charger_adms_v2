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

// 微信路由
Route::any('wechat/pay-push','api/app_v1.wechat/paypush');
Route::any('wechat/oauth-callback','api/app_v1.wechat/oauthcallback');
Route::any('app-entrance','api/app_v1.wechat/command');
Route::any('menus','api/app_v1.wechat/createmenu');


// adms 接口路由