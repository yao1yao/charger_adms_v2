<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/30
 * Time: 上午11:06
 */

namespace app\api\controller\app_v1;


use app\lib\exception\UserInfoException;

class Getnewestbalance extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('getNewestBalance');

        $balance = $this->UserInfoModel->where('id',$data['userId'])->value('pay');
        $freeze = $this->UserInfoModel->where('id',$data['userId'])->value('freeze');
        // 返回该用户的最新余额
        return chargerBack(100,['balance'=>$balance,'freeze'=>$freeze]);

    }
}