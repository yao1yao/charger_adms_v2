<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/17
 * Time: 下午3:32
 */

namespace app\api\controller\app_v1;


use app\api\validate\UserInfoValidate;
use think\Controller;

class Login extends Controller
{
    public function _initialize()
    {
        $this->UserInfo=model('UserInfo');
    }
    public function command(){
        $data = (new UserInfoValidate())->goCheck('login');
        // 判断用户是否合法
        $this->UserInfo->checkUser($data['phone'],$data['password']);
        // 生成登录信息
        $data = $this->UserInfo->createLoginInfo($data['phone']);
        // 返回给前端
        return chargerBack(100,$data);
    }
}