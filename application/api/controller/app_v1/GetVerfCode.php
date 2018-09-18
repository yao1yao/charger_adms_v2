<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 上午10:39
 */

namespace app\api\controller\app_v1;


use AliyunDysmsPhpSdk\Sms;
use app\api\service\VerfCode;
use app\lib\exception\VerfCodeException;
use think\Cache;

class GetVerfCode extends BaseController
{
    public function command()
    {
        $data = $this->UserInfoValidate->goCheck('getVerfCode');
        if(Cache::get($data['phone'])){
            throw new VerfCodeException([
                'respCode'=>50002,
                'errMsg'=>'请一分钟后再获取'
            ]);
        }
        $phone = $data['phone'];
        // 生成六位验证码
        $code = VerfCode::GetRandStr();
        // 发送验证码
        $response = Sms::sendSms($code,$phone);
        if($response->Code === "OK"){
            $res = Cache::set($phone,$code,60);
            if($res){
                return chargerBack(100,["message"=>"发送成功，一分钟有效"]);
            }
        }else{
            return chargerBack(50003,["errMsg"=>"验证码发送失败,请稍后重试"]);
        }
    }
}