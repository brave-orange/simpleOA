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
    public function processChild(){
        if (Request::instance()->isGet()){
            $process_id = input("get.process_id");
            $this->assign("process_id",$process_id);
            $this->assign("roles",model("Auth","service")->getAllroles());
            return $this->fetch();
        }else{
            
        }
    }

    public function addNode(){
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
    public function  processNodes(){
        if (Request::instance()->isGet()){
            $process_id = input("get.process_id");
            $nodes = model("Process","service")->get_process_nodes($process_id);
            $this->assign("nodes",$nodes);
            return $this->fetch();
        }
    }
}