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

}
