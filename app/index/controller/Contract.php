<?php
namespace app\index\controller;
use app\common\controller\CommonController;

class Contract extends CommonController{
    //添加合同总览
    public function contract_oradd(){
        return view();
    }
    //添加销售合同
    public function contract_xadd(){
        return view();
    }
    //添加采购合同
    public function contract_cadd(){
        return view();
    }
    //添加直转合同
    public function contract_zadd(){
        return view();
    }
    //添加销售合同数据
    public function xadd_data(){
        return model('service');
    }
    //添加采购合同数据
    public function cadd_data(){

    }
    //添加直转合同数据
    public function zadd_data(){

    }
}