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
use app\lib\exception\VerfCodeException;
use think\Cache;
use think\exception\ValidateException;
use think\Model;

class UserInfo extends Model
{
    protected $createTime = 'register_date';
    protected $updateTime = 'login_date';
    // 检查登录用户是否合法
    public function checkUser($phone, $password)
    {
        $userInfo=$this->get(['phone'=>$phone]);
        if(!$userInfo){
            throw new UserInfoException([
                'errMsg'=>'用户不存在',
                'respCode'=>40001
            ]);
        }
        if(!password_verify($password,$userInfo['password'])){
            throw new UserInfoException([
                'errMsg'=>'密码不正确',
                'respCode' => 40002
            ]);
        }
    }
    // 注册时，检查用户信息是否已注册, 验证码是否正确
    public function checkUserInfo($phone,$userName,$verCode){
        $result = $this->get(['phone'=>$phone]);
        if($result){
            throw new UserInfoException([
                "errMsg"=>"号码已注册",
                "respCode"=>"40003"
            ]);
        }
        $userName = $this->get(['user_name'=>$userName]);
        if($userName){
            throw new UserInfoException([
                "errMsg"=>"用户名已注册",
                "respCode"=>"40003"
            ]);
        }
        $Code = Cache::get($phone);
        if($Code!==$verCode){
            throw new VerfCodeException([
                "errMsg"=>"验证码不正确",
                "respCode"=>"50004"
            ]);
        }
    }
    // 检查忘记密码用户是否未注册，验证码是否错误
    public function checkUserMessage($phone,$verCode){
        $result = $this->get(['phone'=>$phone]);
        if(empty($result)){
            throw new UserInfoException([
                "errMsg"=>"号码未注册",
                "respCode"=>"40003"
            ]);
        }
        $Code = Cache::get($phone);
        if($Code!==$verCode){
            throw new VerfCodeException([
                "errMsg"=>"验证码不正确",
                "respCode"=>"50004"
            ]);
        }
    }
    // 更新对应用户的密码
    public function updatePassword($phone,$password){
        $result = $this->where('phone',$phone)
            ->update(['password'=>$password]);
        return $result;
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
    public function add($data){
        return $this->save($data);
    }
}