<?php
namespace app\index\service;
use think\Model;
use think\Request;
use think\Session;

class Contract extends Model{
    //销售合同数据
    public function xadd_data(){
        if(Request()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            if($dataobj){
                //生成销售合同id
                $str='XS';
                $contract_id=$this->create_id($str);
                $dataobj['contract_type']="x";
                $dataobj['creator']=Session::get('name');
                $dataobj['create_ip']=$_SERVER['REMOTE_ADDR'];
                $dataobj['contract_id']=$contract_id;
                $res=Model('contract')->data($dataobj)->allowField(true)->save();
                if($res){
                    if($dataobj['cg_contract_id'] !=NULL){
                        $data=['xs_contract_id'=>$contract_id,'cg_contract_id'=>$dataobj['cg_contract_id']];
                        $result=Model('ComContract')->data($data)->save();
                        if($result){
                            return json('success','成功生成合同编号为'.$contract_id.'的合同！');
                        }else{
                            return json('error','添加失败!');
                        }
                    }else{
                        return json('success','成功生成合同编号为'.$contract_id.'的合同！');
                    }
                }else{
                    return json('error','添加失败!');
                }
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
                   $url="public/static/images/" .$file."/". $_FILES[$file]["name"];
                   return json('success',$url);
               }else{
                   return json('error','上传文件失败！');
               }
            }
       }
    }
}