<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/9/30
 * Time: 上午10:25
 */

namespace app\common\model;


use think\Model;

class UserFeedback extends Model
{
    protected $autoWriteTimestamp = 'datetime';
    protected $insert=['status'=>1];
}