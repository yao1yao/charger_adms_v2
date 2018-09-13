<?php

namespace app\lib\exception;

use think\exception\Handle;
use think\log;
class ExceptionHandler extends Handle
{
    /**
     * 接收所有异常，并按照自定义的格式返回
     */
    private $code;
    private $errMsg;
    private $respCode;
    public function render(\Exception $e)
    {
        if($e instanceof BaseException)
        {
            //自定义异常
            $this->code = $e->code;
            $this->errMsg= $e->errMsg;
            $this->respCode=$e->respCode;
        }else{
            if(config('app_debug')){
                return parent::render($e);
            }else{
                $this->code = 200;
                $this->errMsg= $e->getMessage();
                $this->respCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $result = [
            'data'=>[
                "respCode"=>$this->respCode,
                "errMsg"=>$this->errMsg
            ]
        ];
        return json($result,$this->code);
    }
    public function recordErrorLog(\Exception $e)
    {
        Log::init([
            'type'=>'File',
            'path'=>LOG_PATH,
            'level'=>['error'],
        ]);
        Log::record($e->getMessage(),'error');
    }
}