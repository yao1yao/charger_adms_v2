<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/18
 * Time: 下午3:25
 */

namespace app\api\controller\app_v1;

use app\lib\exception\SqlException;

class Forgetpwd extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('forgetPwd');
        $phone = $data['phone'];
        $verfCode = $data['verfCode'];
        // 检查用户是否未注册，验证码是否正确
        $this->UserInfoModel->checkUserMessage($phone,$verfCode);
        // 根据电话号码更新对应字段
        $password = password_hash($data['password'],PASSWORD_DEFAULT);
        $result = $this->UserInfoModel->updatePassword($phone,$password);
        if($result){
            return chargerBack(100,['message'=>'修改成功,请登录']);
        }else{
            throw new SqlException();
        }
    }
}