<?php

namespace app\common\model;


use app\api\service\Token;
use app\lib\exception\NotFoundException;
use app\lib\exception\UserInfoException;
use app\lib\exception\VerfCodeException;
use think\Cache;
use think\Exception;
use think\exception\ValidateException;
use think\Model;

class UserInfo extends Model
{
    protected $createTime = 'register_date';
    protected $updateTime = 'login_date';
    protected $autoWriteTimestamp="datetime";


    /**
     * 登录检查用户信息是否合法
     * @param $phone
     * @param $password
     * @throws UserInfoException
     * @throws \think\exception\DbException
     */
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

    /**
     *
     * 检查验证码是否正确
     * @param $phone
     * @param $verfCode
     * @throws VerfCodeException
     */
    public function checkVerfCode($phone,$verfCode){
        $Code = Cache::get($phone);
        if($Code!==$verfCode){
            throw new VerfCodeException([
                "errMsg"=>"验证码不正确",
                "respCode"=>"50004"
            ]);
        }
    }
    /**
     * 注册时，检查用户信息是否已注册, 验证码是否正确
     * @param $phone
     * @param $userName
     * @param $verCode
     * @throws UserInfoException
     * @throws VerfCodeException
     * @throws \think\exception\DbException
     */
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
        $this->checkVerfCode($phone,$verCode);
    }

    /**
     * 忘记密码时，检查用户信息是否已注册, 验证码是否正确
     * @param $phone
     * @param $verCode
     * @throws UserInfoException
     * @throws VerfCodeException
     * @throws \think\exception\DbException
     */
    public function checkUserMessage($phone,$verCode){
        $result = $this->get(['phone'=>$phone]);
        if(empty($result)){
            throw new UserInfoException([
                "errMsg"=>"号码未注册",
                "respCode"=>"40003"
            ]);
        }
        $this->checkVerfCode($phone,$verCode);

    }
    // 更新对应用户的密码
    public function updatePassword($phone,$password){
        $result = $this->where('phone',$phone)
            ->update(['password'=>$password]);
        return $result;
    }

    /**
     * 生成用户登录成功后，返回的基本信息
     * @param $phone
     * @return array
     * @throws \think\exception\DbException
     */
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
            'phone'=>$userInfo['phone'],
            'openId'=>$userInfo['open_id']
        ];
        return $data;
    }
    public function add($data){
        return $this->save($data);
    }

    /**
     * 判断用户余额是否充足
     * @param $userId   用户Id
     * @param $currentMoney 当前需要花费的余额
     * @throws UserInfoException
     */
    public function isBalanceEnough($userId,$currentMoney){
       $balance = self::where('id',$userId)->column('pay')[0];
       if($balance<$currentMoney){
           throw new UserInfoException([
               'errMsg'=>'账户余额不足',
               'respCode'=>40009
           ]);
       }
    }
    public function modifyDatum($userId,$phone,$userName){
            $oldPhone = self::where('id',$userId)->value('phone');
            $oldUserName = self::where('id',$userId)->value('user_name');
            if($oldPhone===$phone&&$oldUserName===$userName){
                throw new UserInfoException([
                    'errMsg'=>'当前信息并未变更'
                ]);
            }
            $res = $this->where('id',$userId)
                ->update(['phone'=>$phone,'user_name'=>$userName]);
            if(!$res){
                throw new Exception('内部错误');
            }
            return [
                'userName'=>$userName,
                'phone'=>$phone
            ];
    }
}