<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/30
 * Time: 下午2:06
 */

namespace app\api\controller\app_v1;


class Modifydatum extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('modifydatum');
        // 校验验证码
        $this->UserInfoModel->checkVerfCode($data['phone'],$data['verfCode']);
        // 根据用户 ID 更新对应字段
        $result = $this->UserInfoModel->modifyDatum($data['userId'],$data['phone'],$data['userName']);
        return chargerBack(100,$result);
    }

}