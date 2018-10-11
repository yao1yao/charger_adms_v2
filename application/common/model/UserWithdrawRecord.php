<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/10/11
 * Time: 下午4:54
 */

namespace app\common\model;


use app\lib\exception\NotFoundException;
use app\lib\exception\UserInfoException;
use think\Exception;
use think\Model;

class UserWithdrawRecord extends Model
{
    protected $createTime = 'withdraw_time';
    protected $updateTime = 'payment_time';
    protected $autoWriteTimestamp="datetime";


    public function userInfo(){
        return $this->belongsTo('UserInfo','user_id');
    }


    public function withDraw($userId, $money,$openId){

        // 校验金额是否满足条件, 关联获取数据
        $userInfo = $this->userInfo()->where('id',$userId)->find();
        if(!$userInfo){
            throw new NotFoundException([
                'errMsg'=>'该用户不存在'
            ]);
        }
        // 校验金额是否过大
        $this->checkWithMoney($userInfo['pay'],$money);
        // 构造保存数据
        $withdrawRecord=[
            'partner_trade_no'=>generateOrderNumber(),
            'user_id'=>$userId,
            'comment'=>'用户余额提现',
            'withdraw_money'=>$money
        ];
        $result = $this->save($withdrawRecord);
        if(!$result){
            throw new Exception('内部错误');
        }
        // 更新用户冻结余额与可用余额
        $userMoney=[
            'pay'=>$userInfo['pay']-$money,
            'freeze'=>$userInfo['freeze']+$money
        ];
        $result = UserInfo::where('id',$userId)
            ->update($userMoney);
        if(!$result){
            throw new Exception('内部错误');
        }
    }

    /**
     * 判断提现金额是否大于余额
     * @param $balance float 余额
     * @param $money    float 提现金额
     * @throws UserInfoException
     */
    public function checkWithMoney($balance,$money){
        if($balance<$money){
            throw new UserInfoException([
                'respCode'=>40005,
                'errMsg'=>'提现金额过大'
            ]);
        }
    }
}