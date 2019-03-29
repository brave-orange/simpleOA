<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29/029
 * Time: 16:19
 */
namespace app\index\service;
use think\Model;
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
}
