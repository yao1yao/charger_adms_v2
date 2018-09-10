<?php
namespace app\common\model;

use think\Model;

class UserRechargeRecord extends Model
{
    public static function createOrder()
    {

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