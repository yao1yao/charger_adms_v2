<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 下午5:53
 */

namespace app\common\model;


use app\lib\exception\NotFoundException;
use app\lib\exception\SqlException;
use think\Cache;
use think\Model;

class UserChargingRecord extends Model
{
    /**
     * 根据 userId 生成缓存
     * @param $userId   用户 ID
     * @param $data     存储的数据
     * @return bool
     * @throws SqlException
     */
    public function saveChargingRecord($userId,$data){
        $result = $this->save($data);
        if(!$result){
            throw new SqlException();
        }
        return Cache::set($userId,$data);
    }

    /**
     * 从之前的缓存中找到唯一的充电但单号 consumeNumber
     * @param $userId 用户 ID
     */
    public function updateChargingRecord($userId,$energy,$duration)
    {
        $billInfo = Cache::get($userId);
        if(!$billInfo){
            throw new NotFoundException([
                'errMsg'=>'未找到此订单'
            ]);
        }
        $this->where('consume_number',$billInfo['consume_number'])->update([
            'duration'=>$duration,
            'energy'=>$energy
        ]);
    }

}