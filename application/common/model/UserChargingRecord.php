<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/25
 * Time: 下午5:53
 */

namespace app\common\model;


use app\lib\exception\SqlException;
use think\Cache;
use think\Model;

class UserChargingRecord extends Model
{
    public function saveChargingRecord($userId,$data){
        $result = $this->save($data);
        if(!$result){
            throw new SqlException();
        }
        return Cache::set($userId,$data);
    }
}