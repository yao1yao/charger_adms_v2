<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 接口错误日志文件路径
define('LOG_PATH',__DIR__.'/../log/');

// 微信支付推送日志文件路径
define('WECHAT_LOG_PATH',__DIR__.'/../log_wepay/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
