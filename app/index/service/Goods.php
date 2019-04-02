<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29/029
 * Time: 16:19
 */
namespace app\index\service;
use think\Model;
use think\Session;
class Goods extends Model{
    //商品种类
    public function goods_type(){
        return Model('GoodsType')->select();

    }
    //商品添加
    public function goods_add(){
        if(request()->isPost()){
            $name=input('param.name');
            $type=input('param.type');
            $unit=input('param.unit');
            $norms=input('param.norms');
            $manufacturer=input('param.manufacturer');
            $creator=Session::get('name');
            $create_date=date("Y-m-d H:i:s",time());
            $create_ip=$_SERVER['REMOTE_ADDR'];
            $goods_id='SP-'.time();
            if($name && $type && $unit !=""){
                if(Model('goods')->where(['goods_name'=>$name,'goods_type'=>$type,'norms'=>$norms])->select()){
                    return json('error','商品已存在！');
                }
                $data=['goods_id'=>$goods_id,'goods_name'=>$name,'goods_type'=>$type,
                    'unit'=>$unit,'creator'=>$creator,'norms'=>$norms,
                    'create_date'=>$create_date,'manufacturer'=>$manufacturer,'create_ip'=>$create_ip];
                $res=Model('goods')->data($data)->save();
                if($res !=""){
                    return json('success','添加商品成功!');
                }
            }else{
                return json('error','请填写必选项！');
            }

        }
    }
    //商品数据查询
    public function list_data(){
        if(request()->isPost()){
            $page=input("param.page");
//            $limit=($page-1)
        }
    }
    //商品型号
    public function goods_norms(){

    }
    //商品厂家
    public function goods_manufactor(){

    }
}
