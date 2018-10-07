<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/30
 * Time: 下午6:16
 */

namespace app\api\controller\app_v1;


use think\Exception;

class Logout extends BaseController
{
    public function command()
    {
        $data = $this->ApiValidate->goCheck('logout');
        // 更新用户状态为登出
        $res = $this->UserInfoModel->isUpdate(true)->save(['is_login'=>0],['id'=>$data['userId']]);
        if(!$res){
            throw new Exception('内部错误');
        }
        return chargerBack(100,['message'=>'退出成功']);
    }
}