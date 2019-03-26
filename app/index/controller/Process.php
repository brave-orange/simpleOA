<?php
/**
 * 流程控制器类
 * wyc
 */
namespace app\index\controller;
use app\common\controller\CommonController;
class Process{
    public function __construct(){

    }

    public function addProcess(){
        if (Request::instance()->isGet()){
            return $this->fetch();
        }else{
            
        }
    }
    
}