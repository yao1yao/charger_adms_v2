<?php
namespace app\admin\validate;

use think\Validate;

class DeviceOwnerInfo extends BaseValidate
{
    protected $rule=[
        ['notify_url','require|isUrl|max:255','推送地址必填'],
        ['id','require','推送地址必填'],
    ];
    //进行场景设置
    protected $scene=[
        'add'=>['notify_url'],
        'edit'=>['notify_url'],
        'del'=>['id']
    ];
}