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
    public function __construct(){

    }

    public function addProcess(){   //新建流程
        if (Request::instance()->isGet()){
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data['process_name'] = input("post.process_name");
            $data['form_content']  = input("post.form_content");
            $data['creator'] = Session::get('uid');
            $data['create_time'] = data("Y-m-d H:i:s");
            model('Process','service')->add_process();
        }
    }

    public function init_process(){
        if (Request::instance()->isPost()){
            $data = input("post.node_list");
            model('Process','service')->init_process($data);
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
            return view();
        }else{
            
        }
    }
}