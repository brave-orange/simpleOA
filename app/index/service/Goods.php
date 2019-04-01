<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29/029
 * Time: 16:19
 */
namespace app\index\service;
use think\Model;
use think\Request;
use think\Db;
class Goods extends Model{
    //商品种类
    public function goods_type(){
        return Model('GoodsType')->select();

    }
    //商品型号
    public function goods_norms(){

    }
    //商品厂家
    public function goods_manufactor(){

    }
    
    //商品联动
    public function type_join(){
        if(request()->isPost()){
            $goods=input('param.goods');
            Db::table('coa_norms')->where()->select();
        }
    }
    //商品类别联动
    public function join_type(){
        if(request()->isPost()){
            $goods=input("param.goods");
        }
    }
}
