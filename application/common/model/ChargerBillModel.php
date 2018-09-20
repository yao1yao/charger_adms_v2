<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/3/30
 * Time: 上午10:14
 */

namespace app\common\model;


use think\Model;

class ChargerBillModel extends Model
{
    protected $autoWriteTimestamp="datetime";
    /**
     * 关联bill模型
     * @return \think\model\relation\HasMany
     */
    public function bill()
    {
        return $this->hasMany('ChargerInfo','cost_id','id');
    }

    public static function getRate($cost_id)
    {
        return self::with('bill')->find($cost_id);
    }

    public function getAllBillInfo()
    {
        $sdata=[
            'status'=>1
        ];
        $order=[
            'id'=>'desc',
        ];
        $result = $this->where($sdata)
            ->order($order)
            ->select();
        return $result;
    }
    public  function getChargerBillInfo($data=[])
    {
        $sdata = [
            'status'=>1
        ];
        $finaldata = array_merge($sdata,$data);
        $order = [
            'id' => 'desc',
        ];
        $result = $this->where($finaldata)
            ->order($order)
            ->paginate();
        return $result;
    }
}