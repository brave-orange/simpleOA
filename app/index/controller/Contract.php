<?php

namespace app\index\controller;
use app\common\controller\CommonController;
use \think\View;

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
    public function contract_cx(){
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
    //添加商品
    public function contract_info(){
        if(request()->isGet()){
            $contract_id=input("param.contract_id");
            $com_data=Model('ComContract')->field('xs_contract_id')->where('cg_contract_id',$contract_id)->select();
            $array=[];
            foreach($com_data as $key=>&$val){
                $x_data=Model('contract')->where('contract_id',$val['xs_contract_id'])->find();
                $array[$val['xs_contract_id']]=$x_data;
            }
            $c_data=Model('contract')->where('contract_id',$contract_id)->find();
            $array[$contract_id]=$c_data;
            $array['c_id']=$contract_id;
            $array['x_id']=$com_data;
            $view=new View();
            $view->assign('data',$array);
            return $view->fetch();
        }
    }
    //合同添加商品
    public function goods_data(){
        return model('contract','service')->goods_data();
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
    public function updateContractInfo(){
        $data = input("post.data");
        $id = input("post.id");
        $data = json_decode($data,true);
        $c = model("Contract")->get($id);

        if($c->isUpdate(true)->data($data)->save()){
            return json('success','信息更新成功');
        }else{
            return json('error','出现问题了！');
        }
    }
}