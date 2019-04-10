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
use think\Db;
class Goods extends Model{
    //商品种类
    public function goods_type(){
//        return Model('GoodsType')->select();
        return Model('goods')->field('goods_type')->group('goods_type')->select();
    }
    //商品名称
    public function goods_name(){
        return Model('goods')->field('goods_id,goods_name')->select();
    }
    //获取商品型号
    public function get_norms(){
        if(request()->isPost()){
            $goods_id=input("param.goods_id");
            return Model('goods')->field('norms')->where('goods_id',$goods_id)->find();
        }
    }
    //通过联动获取 商品名称
    public function get_names(){
        if(request()->isPost()){
            $goods_type=input("param.type");
            return Model('goods')->field('goods_id,goods_name')->where('goods_type',$goods_type)->select();
        }elseif(request()->isGet()){
            return Model('goods')->field('norms')->select();
        }
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
        if(request()->isGet()) {
            $res = Model('goods')->select();
            $count = count($res);
            return json_decode(json_encode(['code' => 0, 'msg' => '', 'count' => $count, 'data' => $res], JSON_UNESCAPED_UNICODE));
        }elseif(request()->isPost()){
            $page=input("param.page");
            $li=input("param.limit");
            $data=array();
            $name=input('param.name');
            if($name !=""){
                $data=['goods_name'=>$name];
            }
            $type=input('param.type');
            if($type !=""){
                $data=['goods_type'=>$type];
            }
            $norms=input('param.norms');
            if($norms !=""){
                $data=['norms'=>$norms];
            }
            $manufacturer=input('param.manufacturer');
            if($manufacturer !=""){
                $data=['manufacturer'=>$manufacturer];
            }
            if($data){
                $res=Model('goods')->where($data)->page($page,$li)->select();
            }else{
                $res=Model('goods')->select();
            }
            $count=count($res);
            return json_decode(json_encode(['code'=>0,'msg'=>'','count' =>$count,'data'=>$res],JSON_UNESCAPED_UNICODE));
        }
    }
    //厂家数据
    public function manufacturer_data(){
        if(request()->isGet()){
//            model("goods")->group()
            $res=Model('goods')->count('manufacturer');
            var_dump($res);
        }elseif(request()->isPost()){
            $res=Model('goods')->count('manufacturer');
            var_dump($res);
        }
    }

    //商品厂家
    public function goods_manufactor(){

    }
}
