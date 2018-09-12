<?php
namespace app\common\model;

use EasyWeChat\Factory;
use think\Model;
use app\lib\exception\WechatException;

class UserRechargeRecord extends Model
{
    public function createOrder($data)
    {
        $app = Factory::payment(config('Wechat.config'));
        $orderConfig = [
            'body'=>'充电桩支付',
            'attach'=>$data['userId'], // 附加信息为 userId
            'out_trade_no'=>$this->generateOrderNumber(),
            'total_fee'=>$data['rechargeMoney'],
            'trade_type'=>'JSAPI',
            'openid'=>"omPtpwg8ezeS_cVGGROfIzSQUZdw",
            //此处采用内网穿透进行本地调试,所以 ip 必须赋值为外网 ip 地址
            'spbill_create_ip' => '113.91.86.145',//$_SERVER['REMOTE_ADDR']
        ];
        $order = $app->order->unify($orderConfig);
        // 返回错误直接退出
        if($order['return_code'] === 'FAIL'){
            throw new WechatException([
                'errMsg'=>$order['return_msg'],
            ]);
        }else{
            // 生成 jssdk 参数
            $jssdk = $app->jssdk;
            $prepayId = $order['prepay_id'];
        }
        return $order;
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
}