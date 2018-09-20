<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/20
 * Time: 上午11:55
 */

namespace app\common\model;


use app\lib\exception\NotFoundException;
use think\Model;

class DeviceInfo extends Model
{
    protected $autoWriteTimestamp="datetime";
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

}