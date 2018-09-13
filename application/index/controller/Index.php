<?php
/**
 * Created by vscode
 * User: alfred
 * Date: 2018/9/7
 * Time: 上午10:55
 */

namespace app\index\controller;

use EasyWeChat\Factory;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $app = Factory::officialAccount(config('Wechat.config'));
        $response = $app->server->serve();
        $response->send();
    }
}
