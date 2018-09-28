<?php
/**
 * Created by vscode
 * User: alfred
 * Date: 2018/9/7
 * Time: 上午10:55
 */

namespace app\admin\controller;


use think\Controller;
use think\Validate;

class ChargerInfo extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj=model('ChargerInfo');
        $this->validate=validate('ChargerInfo');
    }

    /**
     * 处理搜索逻辑,暂时厂商信息页面
     * @return mixed
     */
    public function index()
    {
        $data=input("param.");
        $sdata=[];
        if(!empty($data['start_time'])&&!empty($data['end_time'])&&
            strtotime($data['end_time'])>strtotime($data['start_time'])){
            $sdata['create_date']=[
                ['gt', $data['start_time']],
                ['lt', $data['end_time']],
            ];
        }
        if(!empty($data['id'])){
            $sdata['id']=$data['id'];
        }
        $userMessage = $this->obj->getDeviceOwnerInfo($sdata);
        return $this->fetch('',[
            'id'=>empty($data['id'])?'':$data['id'],
            'start_time'=>empty($data['start_time'])?'':$data['start_time'],
            'end_time'=>empty($data['end_time'])?'':$data['end_time'],
            'userMessage'=>$userMessage
        ]);
    }

    /**
     * 生成添加页面
     * @return mixed
     */
    public function add()
    {
        $result=$this->obj->column('owner_sn');
        $data=strtolower(\RandString\Serial::getUniqueSerial($result,20));
        return $this->fetch('',[
            'data'=>$data,
        ]);
    }

    /**
     * 保存和更新数据
     */
    public function save()
    {
        if(!request()->isPost()){
            $this->error('请求失败');
        }
        $data=input('param.');
        //没有id 进行更新操作
        if(!empty($data['id'])){
            if(!$this->validate->scene('edit')->check($data)){
                $this->error($this->validate->getError());
            }
            $this->update($data);
        }
        if(!$this->validate->scene('add')->check($data)){
            $this->error($this->validate->getError());
        }
        $res= $this->obj->isUpdate(false)->save($data);
        if($res){
            $this->success('新增成功');
        }else{
            $this->error('新增失败');
        }
    }

    /**
     * 生成编辑页面
     * @return mixed
     */
    public function edit(){
        $id=input("param.id");
        $userMessage=$this->obj->get($id);
        if(!$userMessage){
            $this->error("数据库读取错误");
        }
        return $this->fetch('',[
            'userMessage'=>$userMessage,
        ]);
    }

    /**
     * create watch view
     * @return mixed
     */
    public function watch(){
        $id=input("param.id");
        $userMessage=$this->obj->get($id);
        if(!$userMessage){
            $this->error("数据库读取错误");
        }
        return $this->fetch('',[
            'userMessage'=>$userMessage,
        ]);
    }

    /**
     *
     * @param array $data 需要保存的数组数据
     */
    public function update($data){
        $res=$this->obj->save($data,['id'=>intval($data['id'])]);
        if($res){
            $this->success("更新成功");
        }else{
            $this->error("更新失败");
        }
    }

    /**
     * 删除数据，将对应的status字段置-1
     */
    public function del()
    {
        $data=input('param.');
        if(!$this->validate->scene('del')->check($data)){
            $this->error($this->validate->getError());
        }
        $res=$this->obj->save(['status'=>$data['status']],['id'=>$data['id']]);
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function qcord(){

        return $this->fetch();
    }
}