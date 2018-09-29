<?php
namespace app\common\model;

use app\lib\exception\SqlException;
use EasyWeChat\Factory;
use think\Model;
use app\lib\exception\WechatException;

class UserRechargeRecord extends Model
{
    // 与用户模型的关联

    public function userInfo(){
        return $this->belongsTo('UserInfo','user_id');
    }

    public function createOrder($data)
    {
        $app = Factory::payment(config('Wechat.config'));
        $orderConfig = [
            'body'=>'Charging pile payment',
            'attach'=>$data['userId'], // 附加信息为 userId
            'out_trade_no'=>$this->generateOrderNumber(),
            'total_fee'=>$data['rechargeMoney'],
            'trade_type'=>'JSAPI',
            'openid'=>$data['openId'],
            //此处采用内网穿透进行本地调试,所以 ip 必须赋值为外网 ip 地址
            'spbill_create_ip' => '113.91.86.145',//$_SERVER['REMOTE_ADDR']
        ];
        $order = $app->order->unify($orderConfig);
        // 返回错误直接退出
        if($order['return_code'] === 'FAIL'){
            throw new WechatException([
                'errMsg'=>$order['return_msg'],
                'respCode'=>20002
            ]);
        }else{
            // 配置
            $jssdk = $app->jssdk;
            $prepayId = $order['prepay_id'];
            // 生成支付 JS 配置
            $config = $jssdk->sdkConfig($prepayId);
            // 保存预支付订单
            $this->preSaveRechargeOrder([
                'user_id'=>$data['userId'],
                'recharge_money'=>$data['rechargeMoney']/100,
                'out_trade_no'=>$orderConfig['out_trade_no']
            ]);
                return $config;
        }
    }

    /**
     * 生成支付单号
     * 支付号 = 月日时分秒(10)+随机码(4)+99
     *  例如 120303 + 1223 + 99
     * @param int $type 指定流水类型
     */
    public function generateOrderNumber($type = 99) {
        return date("mdHis").sprintf("%04d", mt_rand(0,9999)).sprintf("%02d",$type);
    }

    /**
     * 预保存支付订单，在订单生成时保存先关信息
     * @param mixed order 订单信息
     */
    public function preSaveRechargeOrder($data){
        if(!$this->save($data)){
            throw new SqlException([
                'respCode'=>30001
            ]);
        }
    }
    /**
     * 获取充值记录
     */
    public function getReChargerRecord($userId){
        $reChargerRecord = $this->where(['user_id'=>$userId])
            ->whereTime('recharge_time','month')
            ->field('id,user_id,recharge_invoice,recharge_remark,transaction_id,out_trade_no',true)
            ->order('recharge_time','desc')
            ->select();
        // 总共充值的钱
        $sumRechargeMoney = $this->where(['user_id'=>$userId])->sum('recharge_money');
        // 用户所剩余额, 暂时不返回，在登录时已经返回过余额
//      $payMoney = self::get(['user_id'=>$userId])->userInfo->pay;
        return [
            'reChargerRecord'=>$reChargerRecord,
            'sumRechargeMoney'=>$sumRechargeMoney,
        ];

    }
}