<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/17
 * Time: 下午3:49
 */

namespace app\common\model;


use app\api\service\Token;
use app\lib\exception\NotFoundException;
use app\lib\exception\UserInfoException;
use think\Cache;
use think\Model;

class UserInfo extends Model
{
    // 检查登录用户是否合法
    public  function  checkUser($phone, $password)
    {
        $userInfo=$this->get(['phone'=>$phone]);
        if(!$userInfo){
            throw new UserInfoException();
        }
        if(!password_verify($password,$userInfo['password'])){
            throw new UserInfoException([
                'errMsg'=>'密码不正确',
                'respCode' => 40002
            ]);
        }
    }
    // 生成登录成功后的返回信息
    public function createLoginInfo($phone){
        // 查询到当前用户信息
        $userInfo=$this->get(['phone'=>$phone]);
        // 构造前端 token
        $key = md5($phone.time());
        $value = Token::generateToken();
        // 放入缓存, 设置过期时间为一周
        Cache::set($key,$value,604800);
        // 更改用户为登录状态
        $this->isUpdate(true)->save(['is_login'=>1],['phone'=>$phone]);
        $data = [
            'token'=>$key,
            'consume'=>$userInfo['consume'],
            'userId'=>$userInfo['id'],
            'userName'=>$userInfo['user_name'],
            'balance'=>$userInfo['pay'],
        ];
        return $data;
    }

}