<?php
/**
 * 流程控制器类
 * wyc
 */
namespace app\index\controller;
use app\common\controller\CommonController;
use think\Request;

use think\Session;
class Process extends CommonController{


    public function addProcess(){   //新建流程
        if (Request::instance()->isGet()){
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data['process_name'] = input("post.process_name");
            $data['form_content']  = input("post.form_content");
            $data['creator'] = Session::get('uid');
            $data['create_time'] = date("Y-m-d H:i:s");
            $data['status'] = 0;
            return model('Process','service')->add_process($data);
        }
    }

    public function init_process(){
        if (Request::instance()->isPost()){
            $data = input("post.node_list");
            //$node_list = json_decode($data,true);

            if(model('Process','service')->init_process($data)){
                return json('success','初始化成功！');
            }else{
                return json('error','出现问题了！');
            }

        }
    }


    public function editProcess(){
        if (Request::instance()->isGet()){
            return $this->fetch();
        }else if (Request::instance()->isPost()){
        }
    }
    public function delProcess(){
        if (Request::instance()->isPost()){
            $processid = input("post.process_id");
            if(model("Process")->del_process($processid)){
                return json('success','删除成功！');
            }else{
                return json('error','出现问题了！');
            }
        }
    }

    public function getprocesslist_available(){   //已初始化流程
        if (Request::instance()->isPost()){
            $page = input('param.page');
            $limit = input('param.limit');
            $start = ($page-1)*$limit;
            return model('Process','service')->get_process_list($start,$limit,1);
        }
    }
    public function getprocesslist_unavailable(){    //未初始化流程
        if (Request::instance()->isPost()){
            $page = input('param.page');
            $limit = input('param.limit');
            $start = ($page-1)*$limit;
            return model('Process','service')->get_process_list($start,$limit,0);
        }
    }
    public function processChild(){    //初始化节点对话框
        if (Request::instance()->isGet()){
            $process_id = input("get.process_id");
            $this->assign("process_id",$process_id);
            $this->assign("roles",model("Auth","service")->getAllroles());
            return $this->fetch();
        }else{
            
        }
    }

    public function addNode(){    //添加节点
        if (Request::instance()->isGet()){
            $process_id = input("get.process_id");
            $this->assign("process_id",$process_id);
            $this->assign("roles",model("Auth","service")->getAllroles());
            $nodes = model("Process","service")->get_process_nodes($process_id);
            $this->assign("nodes",$nodes);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data = input("post.data");
            $res = model("Process","service")->insert_node($data);
            if($res){
                return json('success','添加成功！',$res);
            }else{
                return json('error','出现问题了！');
            }
            
        }
    }
    public function  processNodes(){    //查看流程的所有节点
        if (Request::instance()->isGet()){
            $process_id = input("get.process_id");
            $nodes = model("Process","service")->get_process_nodes($process_id);
            $this->assign("nodes",$nodes);
            return $this->fetch();
        }
    }

    public function contractProcess(){    //进入合同审批流程
        if (Request::instance()->isGet()){
            $contract_id = input("get.contract_id");
            $contract_data = model("Contract","service")->getContactDetail($contract_id);
            
            $action = model("Process")->get("PR-20190404-1")["action"];
            //dump($contract_data['detail']);
            if(model("Contract")->getContractType($contract_id) == "销售合同"){
                $contract_data['contract_type'] = "销售";
            }else{
                $contract_data['contract_type'] = "采购";
            }
            $this->assign('res',$contract_data);
            return $this->fetch($action);
        }else if(Request::instance()->isPost()){
            $process_data = input("post.process_data");
            $res = model("Process","service")->startProcess("PR-20190404-1",$process_data);    //添加合同审批任务
            $contract_id = json_decode($process_data,true)['contract_id'];
            model("Contract")->isUpdate(true)->where(["contract_id"=>$contract_id])->update(['task_id'=>$res]);//写入taskID
            if($res){
                return json('success','提交审批成功，合同已进入审批流程！');
            }else{
                return json('error','出现问题了！');
            }
        }
    }

    public function checkTask(){
        if (Request::instance()->isGet()){
            $task_id = input("get.task_id");
            $cache_id = input("get.cache_id");
            $his = model("Process","service")->getTaskHistory($task_id);
            $res = model("ProcessTask")->where(["task_id"=>$task_id])->find()["content"];
            $res = json_decode($res,true);
            $this->assign("res", $res);
            $this->assign("history",$his);
            $this->assign("cache_id",$cache_id);
            return $this->fetch();
        }
    }
    public function getTaskByCache(){    //根据任务缓存信息获取任务信息
        if (Request::instance()->isGet()){
            $cid = input('get.id');
            $res = model("Process","service")->getTaskInfoByCache();
            $content = json_decode($res,true);
            $this->assign('res',$content);
            return $this->fetch($res["action"]);
        }else if(Request::instance()->isPost()){

        }
    }
    public function processTest(){
         dump(model("Process","service")->getTaskinfo());
         
    }

    public function myTask(){
        if (Request::instance()->isGet()){
            return $this->fetch();
        }else if(Request::instance()->isPost()){
            $page = input('param.page');
            $limit = input('param.limit');
            $type = input('param.type');
            $start = ($page-1)*$limit;
            $tasks = model("Process","service")->getTaskinfo($start,$limit,$type,true);
            return $tasks;
        }
    }

    public function returnTaskTypepage(){
        if (Request::instance()->isGet()){
            $page = input("get.page_name");
            return $this->fetch($page);
        }
    }



}