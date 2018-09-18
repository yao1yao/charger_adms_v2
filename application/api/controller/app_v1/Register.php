<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 上午11:58
 */

namespace app\api\controller\app_v1;


use app\lib\exception\SqlException;
use think\Controller;

class Register extends BaseController
{
    public function command()
    {
        $data = $this->UserInfoValidate->goCheck('register');
        // 判断用户是否已注册，验证码是否正确
        $this->UserInfoModel->checkUserInfo($data['phone'],$data['userName'],$data['verfCode']);
        // 构造需要存入数据库的信息
        $sdata = [
          'user_name'=>$data['userName'],
          'phone'=>$data['phone'],
          'password'=>password_hash($data['password'],PASSWORD_DEFAULT),
          'open_id'=>$data['openId'],
        ];
        $result = $this->UserInfoModel->add($sdata);
        if($result){
            return chargerBack(100,["message"=>"注册成功，请登录"]);
        }else{
            throw new SqlException([
                'respCode'=>'30002'
            ]);
        }
    }
}