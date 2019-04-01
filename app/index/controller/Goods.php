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

	//商品添加
	public function goods_add(){
	    return view();
    }

    //商品规格添加
	public function goods_specifications(){
		return view();
	}
    //商品种类添加
	public function goods_category(){
		return view();
	}
	//商品厂商添加
	public function goods_manufacturer(){
		return view();
	}
    //商品联动
    public function type_join(){
	    return model('goods','service')->type_join();
    }
    //商品类别联动
    public function join_type(){
        return model('goods','service')->join_type();
    }
}
