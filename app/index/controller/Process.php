<?php
/**
 * 流程控制器类
 * wyc
 */
namespace app\index\controller;
use app\common\controller\CommonController;
use think\Request;
class Process extends CommonController{
    public function __construct(){

    }

    public function addProcess(){
        if (Request::instance()->isGet()){
            return view();
        }else{
            
        }
    }
    public function processChild(){
        if (Request::instance()->isGet()){
            return view();
        }else{
            
        }
    }
    
}