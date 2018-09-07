<?php

namespace app\common\model;
use think\Model;
class DeviceOwnerInfo extends Model{
    // 设置自动写入时间格式
    protected $autoWriteTimestamp="datetime";
    public function getDeviceOwnerInfo($data=[])
    {
        $sdata=[
            'status'=>1
        ];
        $finaldata = array_merge($sdata, $data);
        $order=[
            'id'=>'desc',
        ];
         $result = $this->where($finaldata)
            ->order($order)
            ->paginate();
        return $result;
    }
}
