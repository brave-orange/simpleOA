layui.use(['layer','table'], function() {
    var $ = layui.jquery,
    form = layui.form,
    table = layui.table;
    table.render({
        elem: '#contract_tasklist'
        ,url:"mytask"
        ,method:'post'
        ,page:true
        ,where:{type:"PR-20190404-1"}
        ,cols: [[
        {field:'id', hide:true, title: 'ID'}
        ,{field:'contract_id', hide:false,minWidth:120, title: '合同编号'}
        ,{field:'task_id', hide:false,minWidth:120, title: '审批编号'}
        ,{field:'create_person', align:'center', minWidth:80, title: '发起人'}
        ,{field:'business_name',minWidth:80, title: '供应商名称'}
        ,{field:'money', align:'center', minWidth:80, title: '总价'}
        ,{field:'signtime', align:'center', minWidth:80, title: '签订时间'}
        ,{field:'contract_file', align:'center', minWidth:80, title: '采购合同副本'}
        ,{field:'status', align:'center', minWidth:80, title: '当前流程'}
        ,{field:'last_person', align:'center', minWidth:120, title: '上一审核人'}
        ,{fixed: 'right', align:'center',minWidth: 120, title:'管理',align:'center', toolbar: '#barDemo'}
        ]]
      ,even: true //开启隔行背景
      ,done:function(data){
        if(data.count == 0){
            $(".layui-none").text("暂无您需要审批的内容...");
        }
      }
});
    table.on('tool(bar)', function(obj){ 
        var data = obj.data; 
        var layEvent = obj.event; 
        var tr = obj.tr;
        if(layEvent === 'checkTask'){ 
            var task_id = data.task_id;
            var cache_id = data.id;
            layer.load(); //loadings
            $.get("index/process/checkTask",{cache_id:cache_id,task_id:task_id},function(data){
                layer.open({
                      title:'查看审批内容',
                      offset: ['50px'],
                      type: 1,
                          skin: 'layui-layer-rim', //加上边框
                      btn:["允许","驳回"],
                      btnAlign:"c",
                      area: ['60%','80%'], //宽高
                          content: data,
                          scrollbar:true
                         
                    });
                     layer.closeAll('loading'); //关闭loading
            })
        }
    })
})