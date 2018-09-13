<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate{
    public function goCheck($sceneName){
        // 获取 http 传入的参数
        $request = Request::instance();
        $params = $request->param();
        $result = $this->scene($sceneName)->check($params);
        if(!$result){
            throw new ParameterException([
                'errMsg'=>$this->getError()
            ]);
        }else{
            return $params;
        }
    }
    protected function isNotEmpty($value, $rule='',$data='',$field='')
    {
        if($value===''){
            return $field. "不能为空";
        }else{
            return true;
        }
    }
    protected function isMobile($value, $rule='',$data='',$field='')
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return "电话号码不正确";
        }
    }
    protected function isMsgId($value, $rule='',$data='',$field='')
    {
        $rule = "^[a-zA-Z\d_]{8}$^";
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return "msgId 非法";
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
}