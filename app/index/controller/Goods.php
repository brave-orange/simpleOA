<?php
namespace app\index\controller;
use think\Controller;
use app\common\controller\CommonController;
use think\Request; 
class Goods extends CommonController
{
    //商品列表
	public function goods_list(){
		return view();
	}
	//获取商品类别
    public function get_norms(){
	    return model('goods','service')->get_norms();
    }
	//通过联动获取 商品名称
    public function get_names(){
	    return model('goods','service')->get_names();
    }
    //商品列表数据
    public function list_data(){
	    return model('goods','service')->list_data();
    }
	//商品添加
	public function goods_add(){
	    return model('goods','service')->goods_add();
    }
    //商品规格添加
	public function goods_specifications(){
	    if(Request::instance()->isGet()){
            return view();
        }
	}
	//商品厂家列表
    public function manufacturer_data(){
	   return model('goods','service')->manufacturer_data();
    }
    //商品种类添加
	public function goods_category(){
		return view();
	}
	//商品厂商添加
	public function goods_manufacturer(){
		return view();
	}

}
