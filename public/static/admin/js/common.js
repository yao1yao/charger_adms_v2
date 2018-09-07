
function member_edit(title,url){
    let index=layer.open({
        type:2,
        title:title,
        content: url
    });
    layer.full(index);
}
/*添加或者编辑缩小的屏幕*/
function member_s_edit(title,url,w,h){
    layer_show(title,url,w,h);
}

function member_add(title,url,w,h){
     layer_show(title,url,w,h);

}
function memeber_watch(title,url){
    let index=layer.open({
        type:2,
        title:title,
        content: url
    });
    layer.full(index);
}

function member_del(url){
    layer.confirm('确认要删除吗?',function(index){
        window.location.href=url;
    });
}

//日期时间插件
function selecttime(flag){
    if(flag===1){
        let endTime = $("#countTimeend").val();
        if(endTime !== ""){
            WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:endTime})}else{
            WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})}
    }else{
        let startTime = $("#countTimestart").val();
        if(startTime !== ""){
            WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:startTime})}else{
            WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})}
    }
}

//根据id设置文本内容
function changeVal(id, data) {
    $(id).val(data);
}
