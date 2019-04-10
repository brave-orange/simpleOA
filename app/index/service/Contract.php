<?php
namespace app\index\service;
use think\Model;
use think\Request;
use think\Session;
use think\Db;
class Contract extends Model{
    //销售合同数据
    public function xadd_data(){
        if(Request()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            $goods=[];
            if($dataobj['goodsarr']==''){
                return json('error','请填写商品信息！');
            }else{
                foreach ($dataobj['goodsarr'] as $key=>&$val){
                   $goods['price']= $val['price'];
                   $goods['good_id']=$val['name'];
                   $goods['count']=$val['num'];
                   $goods['total']=$val['price']*$val['num'];
                   $goods['currency']='RMB';
                }
            }
            if($dataobj['dataobj'] !=""){
                //生成销售合同id
                $str='XS';
                $contract_id=$this->create_id($str);
                $goods['contract_type']="x";
                $goods['creator']=Session::get('name');
                $goods['create_ip']=$_SERVER['REMOTE_ADDR'];
                $goods['contract_id']=$contract_id;
                $goods['contract_file']=$dataobj['dataobj']['contract_file'];
                $goods['money']=$dataobj['dataobj']['money'];
                $goods['business_name']=$dataobj['dataobj']['business_name'];
                $goods['contract_type']='c';
                $goods['createdate']=date("Y-m-d H:i:s",time());
                $goods['dateofcollection']=$dataobj['dataobj']['dateofcollection'];
                $res=Model('contract')->data($goods)->allowField(true)->save();
                if($res){
                    if($dataobj['dataobj']['cg_contract_id'] !=''){
                        Model('ComContract')->data(['cg_contract_id'=>$dataobj['dataobj']['cg_contract_id'],'xs_contract_id'=>$contract_id])->save();
                    }
                        return json('success','成功生成合同编号为'.$contract_id.'的合同！');

                }else{
                    return json('error','添加失败!');
                }
            }else{
                return json('error','请填写合同信息！');
            }
        }
    }
    //生成合同编号
    protected function create_id($str){
        $id=Session::get('id');
        $c=$str.date("ymd").$id.chr(mt_rand(97,122)).mt_rand(10,99);
        $res=Model('contract')->where('contract_id',$c)->find();
        if($res){
            $this->create_id($str);
        }
        return $c;
    }
    //采购合同数据
    public function cadd_data(){
        if(Request::instance()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            if($dataobj){
                //生成销售合同id
                $str='CG';
                $contract_id=$this->create_id($str);
                $dataobj['contract_type']="c";
                if($dataobj['earnest_money']>$dataobj['money']){ return json('error','定金不能大于采购金额！');}
                $dataobj['creator']=Session::get('name');
                $dataobj['create_ip']=$_SERVER['REMOTE_ADDR'];
                $dataobj['contract_id']=$contract_id;
                $res=Model('contract')->data($dataobj)->allowField(true)->save();
                if($res){
                    $result=Model('contract')->where('contract_id',$dataobj['xs_contract_id'])->find();
                    if($result !=NULL){
                        $data=['cg_contract_id'=>$contract_id,'xs_contract_id'=>$dataobj['xs_contract_id']];
                        $result=Model('ComContract')->data($data)->save();
                        if($result){
                            return json('success','成功生成合同编号为'.$contract_id.'的合同！');
                        }else{
                            return json('error','添加失败!');
                        }
                    }else{
                        return json('error','请输入正确的销售合同编号！');
                    }
                }else{
                    return json('error','添加失败!');
                }
            }
        }
    }
    //直转合同数据
    public function zadd_data(){
        if(Request::instance()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            if($dataobj){
                $str='CG';
                $c_contract_id=$this->create_id($str);
                $strs='XS';
                $x_contract_id=$this->create_id($strs);
                $xdata=array();
                $xdata['contract_id']=$c_contract_id;
                $xdata['business_name']=$dataobj['xname'];
                $xdata['money']=$dataobj['xmoney'];
                $xdata['dateofcollection']=$dataobj['xdateofcollection'];
                $xdata['remarks']=$dataobj['remarks'];
                $xdata['createdate']=$dataobj['createdate'];
                $xdata['creator']=Session::get('name');
                $xdata['create_ip']=$_SERVER['REMOTE_ADDR'];
                $cdata=array();
                $cdata['contract_id']=$x_contract_id;
                $cdata['earnest_money']=$dataobj['emoney'];
                $cdata['money']=$dataobj['cmoney'];
                $cdata['business_name']=$dataobj['cname'];
                $cdata['dateofcollection']=$dataobj['cdateofcollection'];
                $cdata['remarks']=$dataobj['remarks'];
                $cdata['createdate']=$dataobj['createdate'];
                $cdata['creator']=Session::get('name');
                $cdata['create_ip']=$_SERVER['REMOTE_ADDR'];
                $res=Model('contract')->data($xdata)->save();
                $result=Model('contract')->data($cdata)->save();
                if($res && $result){
                    $zdata=['xs_contract_id'=>$x_contract_id,'cg_contract_id'=>$c_contract_id];
                    $r=Model('ComContract')->data($zdata)->save();
                    if($r){
                        return json('success','成功生成销售编号为'.$x_contract_id.'和采购编号为'.$c_contract_id.'的合同！');
                    }
                }
            }
        }
    }
    //合同总览数据
    public function contract_zdata(){
        if(request()->isPost()){
            $p=input('param.page');
            $page=($p-1)*10;
            $where="";
            $contract_status=input('param.contract_status');
            if($contract_status){
                $where.=" and contract_status=$contract_status";
            }
            $name=input('param.name');
            if($name){
                $where.=" and business_name=$name";
            }
            $time=input('param.sk_time');
            if($time){
                $time=explode(' - ',$time);
                $where.=" and $time[1]>dateofcollection>$time[0]";
            }
            $sql ="select * from coa_contract a JOIN coa_com_contract b ON a.contract_id=b.cg_contract_id where contract_type='c' $where limit $page,10";
            $res=Db::query($sql);
            $count=count($res);
            return json_decode(json_encode(['code'=>0,'msg'=>'','count' =>$count,'data'=>$res],JSON_UNESCAPED_UNICODE));

        }
    }
    //采购合同列表数据
    public function contract_cdata(){
        if(request()->isPost()){
            $p=input('param.page');
            $page=($p-1)*10;
            $where="";
            $contract_status=input('param.contract_status');
            if($contract_status){
                $where.=" and contract_status=$contract_status";
            }
            $name=input('param.name');
            if($name){
                $where.=" and business_name=$name";
            }
            $time=input('param.sk_time');
            if($time){
                $time=explode(' - ',$time);
                $where.=" and $time[1]>dateofcollection>$time[0]";
            }
            $sql ="select * from coa_contract a JOIN coa_com_contract b ON a.contract_id=b.cg_contract_id where contract_type='c' $where limit $page,10";
            $res=Db::query($sql);
            $count=count($res);
            return json_decode(json_encode(['code'=>0,'msg'=>'','count' =>$count,'data'=>$res],JSON_UNESCAPED_UNICODE));

        }
    }
    //销售合同列表数据
    public function contract_xdata(){
        if(request()->isPost()){
            $p=input('param.page');
            $page=($p-1)*10;
            $where="";
            $contract_status=input('param.contract_status');
            if($contract_status){
                $where.=" and contract_status=$contract_status";
            }
            $name=input('param.name');
            if($name){
                $where.=" and business_name=$name";
            }
            $time=input('param.sk_time');
            if($time){
                $time=explode(' - ',$time);
                $where.=" and $time[1]>dateofcollection>$time[0]";
            }
            $sql ="select * from coa_contract a JOIN coa_com_contract b ON a.contract_id=b.xs_contract_id where contract_type='x' $where limit $page,10";
            $res=Db::query($sql);
            foreach($res as $key=>$val){
                if($res[$key]['contract_status']=='0'){
                    $res[$key]['contract_status']='未审核';
                }elseif($res[$key]['contract_status']=='1'){
                    $res[$key]['contract_status']='已审核';
                }elseif($res[$key]['contract_status']=='3'){
                    $res[$key]['contract_status']='已完成';
                }
                if($res[$key]['contract_type']=='x'){
                    $res[$key]['contract_type']='销售合同';
                }
            }
            $count=count($res);
            return json_decode(json_encode(['code'=>0,'msg'=>'','count' =>$count,'data'=>$res],JSON_UNESCAPED_UNICODE));

        }
    }
    //合同商品添加
    public function goods_data(){
        if(request()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            if($dataobj['goods_price'] < 0 && $dataobj['goods_price'] ==0){
                return json('error','请填写正确的商品单价！');
            }
            if($dataobj['number'] <0 && $dataobj['number']=='0'){
                return json('error','请填写正确的商品数量！');
            }
            $dataobj['zmoney']=$dataobj['number']*$dataobj['goods_price'];
            return json_decode(json_encode(['code'=>0,'msg'=>'','data'=>$dataobj],JSON_UNESCAPED_UNICODE));
        }
    }
    //图片回调
    public function images(){
       if(Request::instance()->param(true)){
            if($_FILES){
                $key=array_keys($_FILES);
                if($key[0]=='xadd'){
                    $file='xadd';
                }elseif($key[0]=='cadd'){
                    $file='cadd';
                }elseif($key[0]=='zadd'){
                    $file='zadd';
                }else{
                    return json('error','请选择上传文件!');
                }
                if($_FILES[$file]['size']>2*1024*1024){ return json('error','上传文件太大无法上传！');}
               $data=move_uploaded_file($_FILES[$file]["tmp_name"],
                   ROOT_PATH."public/static/images/" .$file."/". $_FILES[$file]["name"]);
               if($data){
                   $url=$file."/". $_FILES[$file]["name"];
                   return json('success',$url);
               }else{
                   return json('error','上传文件失败！');
               }
            }
       }
    }
}