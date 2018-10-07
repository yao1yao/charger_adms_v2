<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/4/4
 * Time: 下午2:58
 */

namespace app\common\model;


use think\Model;

class DeviceNotifyInfo extends Model
{
    protected $autoWriteTimestamp="datetime";
    public function getDeivceStatus()
    {
        $result =$this->field('deviceId,status,create_time')
            ->whereTime('create_time', 'week')->select();
        return $result;
    }
    public function notifyMessage($data=[]){
        $order=[
            'id'=>'desc',
        ];
        $result=$this->where($data)
            ->order($order)
            ->limit(100)
            ->paginate();
        return $result;
    }

}