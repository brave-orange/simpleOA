layui.use(['layer','table'], function() {
    var $ = layui.jquery,
    table = layui.table,
    layer = layui.layer;
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
                      area: ['65%','80%'], //宽高
                      content: data,
                      scrollbar:true,
                      yes:function(index,layero){
                        var data = $(".layui-layer-content").find("form").serialize();
                        console.log(data);
                      },
                      btn2:function(index){
                        return false;
                      }
                    });
                     layer.closeAll('loading'); //关闭loading
            })
        }
    })
})

function satrtprocess(){
    var content = $(".layui-layer-content").find("#start_content").find("form").serialize();
    var other =  $(".layui-layer-content").find("#other").serialize();
    content = decodeURIComponent(content,true);
    other = decodeURIComponent(other,true);
    var data = {};
    var temp= content.split("&");
    var res = {};
    for( var i = 0; i < temp.length; i++){
        var value = temp[i].split("=")
        res[value[0]] = value[1];
    }
    var detail = $(".layui-layer-content").find("#start_content").find("table")
    if (detail.length != 0){
        var tab = [];
        detail.find("tbody").find("tr").each(function(j,it){
            var id = {};
            $(this).find("td").each(function(i,item){
              id[$(this).attr("class")] = $(this).html();
          })
            tab.push(id);
        })
    }
    res ["content"] = tab;
    var str_res = JSON.stringify(res);
    var stamp = $("input[name='stamp']:checked").val();
    if(typeof(stamp) == "undefined"){
        layer.msg("请选择盖章类型！")
        return false;
    }
    var remark = $("input[name='remark']").val();
    var process_id = $("input[name='process_id']").val();
    var id = $("input[name='id']").val();
    var res;
    $.ajax({
        url:"/index/process/startProcess",
        method:"post",
        async:false,
        data:{process_data:str_res,stamp:stamp,process_id:process_id,id:id,remark:remark},
        success:function(data){
        data = JSON.parse(data);
            if(data.status == 'error'){
                layer.msg(data.msg,{icon: 5});//失败的表情

            }else if(data.status == 'success'){
                var task_id = data.data.task;
                res = task_id;
               
            }
      }
    })
    return res;
}

function contract_process_start(){
    var res = satrtprocess();
    if(res){
        var data = {};
        var res_str = JSON.stringify({task_id:res});
        data["data"] = res_str;
        data["id"] = $("input[name='id']").val();
        $.post("/index/contract/updateContractInfo",data,function(data1){
            if(data1.status == 'success'){
                layer.msg(data.msg, {
                    icon: 6,//成功的表情
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                }, function(){
                    location.reload();
                }); 
            }else if(data1.status == 'error'){
                layer.msg(data.msg,{icon: 5});
                return false;
            }
        })

    }else{
        console.log("??")
    }
}