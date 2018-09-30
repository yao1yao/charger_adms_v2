<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/30
 * Time: 上午10:20
 */

namespace app\api\controller\app_v1;


use app\lib\exception\NotFoundException;
use app\lib\exception\UserInfoException;
use think\Exception;

class Feedback extends BaseController
{
    public function command(){
        $data = $this->ApiValidate->goCheck('feedback');

        $result = $this->UserInfoModel->get($data['userId']);

        if(!$result){
            throw new NotFoundException([
                'errMsg'=>'用户不存在'
            ]);
        }
        $saveData = [
            'tag'=>$data['tag'],
            'content'=>$data['content'],
            'user_id'=>$data['userId']
        ];
        $result = $this->UserFeedbackModel->isUpdate(false)->save($saveData);

        if(!$result){
            throw new Exception('内部错误');
        }
        return chargerBack(100,["message"=>"反馈成功"]);
    }
}