<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/10/11
 * Time: 下午3:31
 */

namespace app\api\controller\app_v1;


class withDraw extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('withDraw');
        // 验证码处理
        $this->UserInfoModel->checkVerfCode($data['phone'],$data['verfCode']);
        // 进行提现处理
        $this->UserWithdrawRecordModel->withDraw($data['userId'],$data['money'],$data['openId']);
        return chargerBack(100,['message'=>'提现成功']);
    }
}