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
    //销售合同列表
    public function contract_xlist(){
        return view();
    }
    //采购合同列表
    public function contract_clist(){
        return view();
    }
    //合同总览
    public function contract_zlist(){
        return view();
    }
    //合同总览数据
    public function contract_zdata(){
        return model('contract','service')->contract_zdata();
    }
    //销售合同数据
    public function contract_xdata(){
        return model('contract','service')->contract_xdata();
    }
    //采购合同数据
    public function contract_cdata(){
        return model('contract','service')->contract_cdata();
    }
    //添加销售合同数据
    public function xadd_data(){
        return model('contract','service')->xadd_data();
    }
    //添加采购合同数据
    public function cadd_data(){
        return model('contract','service')->cadd_data();
    }
    //添加直转合同数据
    public function zadd_data(){
        return model('contract','service')->zadd_data();
    }
    public function images(){
        header("content-type:text/html;charset=utf-8");
        return model('contract','service')->images();
    }

}