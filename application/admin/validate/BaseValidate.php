<?php

namespace app\admin\validate;

use think\Validate;

class BaseValidate extends Validate
{
    protected function isUrl($value, $rule='',$data='',$field='')
    {
        $rule = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return "请输入正确的url地址";
        }
    }
    protected function isPositiveInteger($value, $rule='',$data='',$field='')
    {
        //判断是否为正整数
        if(is_numeric($value)&& is_int($value+0)&&($value+0)>0)
        {
            return true;
        }else{
            return $field.'必须是正整数';
        }
    }
    protected  function checkTime($value, $rule='',$data='',$field='')
    {
        if(strtotime($value)<time()){
            return '不能小于当前时间';
        }else{
            return true;
        }
    }
}