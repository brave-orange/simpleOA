<?php
namespace app\index\service;
use think\Model;
use think\Request;
use think\Session;
use think\Db;
class Contract extends Model
{
    //销售合同数据
    public function xadd_data()
    {
        if (Request()->isPost()) {
            $data = input('param.formdata');
            $dataobj = json_decode($data, true);
            $goods = [];
            if ($dataobj['goodsarr'] == '') {
                return json('error', '请填写商品信息！');
            }
            if ($dataobj['dataobj'] == "") {
                return json('error', '请填写合同信息！');
            }
            //生成销售合同id
            $str = 'XS';
            $contract_id = $this->create_id($str);

                foreach ($dataobj['goodsarr'] as $key => &$val) {
                    $con = Model('ContractDetail')->count();
                    $goods['id'] = 'IF'.date('ymd',time())."-".($con + 1);
                    $goods['price'] = $val['price'];
                    $goods['good_id'] = $val['name'];
                    $goods['count'] = $val['num'];
                    $goods['total'] = $val['price'] * $val['num'];
                    $goods['currency'] = 'RMB';
                    $goods['contract_type']='x';
                    $goods['contract_id']=$contract_id;
                    $res = Model('ContractDetail')->data($goods)->save();
                    if ($res == "") {
                        return json('error', '添加失败!');
                    }
                }
                $table=[];
                $table['creator'] = Session::get('name');
                $table['create_ip'] = $_SERVER['REMOTE_ADDR'];
                $table['contract_id'] = $contract_id;
                $table['contract_file'] = $dataobj['dataobj']['xadd'];
                $table['money'] = $dataobj['dataobj']['money'];
                $table['business_name'] = $dataobj['dataobj']['business_name'];
                $table['contract_type'] = 'x';
                $table['createdate'] = date("Y-m-d H:i:s", time());
                $table['dateofcollection'] = $dataobj['dataobj']['dateofcollection'];
                $res = Model('contract')->data($table)->allowField(true)->save();
                if ($res) {
                    return json('success', '成功生成合同编号为' . $contract_id . '的合同！');
                } else {
                    return json('error', '添加失败!');
                }

        }
    }

    //生成合同编号
    protected function create_id($str)
    {
        $id = Session::get('id');
        if($str=="XS"){
            $count = Model('contract')->where('contract_type','x')->count();
        }else{
            $count = Model('contract')->where('contract_type','c')->count();
        }

        $c = $str . date("ymd") . $id . chr(mt_rand(97, 122)) . '-' . ($count + 1);
        $res = Model('contract')->where('contract_id', $c)->find();
        if ($res) {
            $this->create_id($str);
        }
        return $c;
    }

    //采购合同数据
    public function cadd_data()
    {
        if (Request::instance()->isPost()) {
            $data = input('param.formdata');
            $dataobj = json_decode($data, true);
            $goods = [];
            if ($dataobj['dataobj'] != "") {
                $result = Model('contract')->where('contract_id', $dataobj['dataobj']['xs_contract_id'])->find();
                if ($result == "") {
                    return json('error', '请输入正确的销售合同编号！');
                }
            } else {
                return json('error', '请填写合同信息！');
            }
            //生成采购合同id
            $str = 'CG';
            $contract_id = $this->create_id($str);
            $contract['contract_type'] = "c";
            if ($dataobj['dataobj']['e_money'] > $dataobj['dataobj']['money']) {
                return json('error', '定金不能大于采购金额！');
            }

            $contract['creator'] = Session::get('name');
            $contract['create_ip'] = $_SERVER['REMOTE_ADDR'];
            $contract['contract_id'] = $contract_id;
            $contract['contract_file'] = $dataobj['dataobj']['contract_file'];
            $contract['money'] = $dataobj['dataobj']['money'];
            $contract['e_money'] = $dataobj['dataobj']['earnest_money'];
            $contract['business_name'] = $dataobj['dataobj']['business_name'];
            $contract['contract_type'] = 'c';
            $contract['createdate'] = date("Y-m-d H:i:s", time());
            $contract['dateofcollection'] = $dataobj['dataobj']['dateofcollection'];
            if ($dataobj['goodsarr'] != "") {
                foreach ($dataobj['goodsarr'] as $key => &$val) {
                    $con = Model('ContractDetail')->count();
                    $goods['id'] = 'IF'.date('ymd',time())."-".($con + 1);
                    $goods['price'] = $val['price'];
                    $goods['good_id'] = $val['name'];
                    $goods['count'] = $val['num'];
                    $goods['total'] = $val['price'] * $val['num'];
                    $goods['currency'] = 'RMB';
                    $goods['contract_type'] = 'c';
                    $goods['contract_id'] = $contract_id;

                    $res = Model('ContractDetail')->data($goods)->save();
                    if ($res == "") {
                        return json('error', '添加失败!');
                    }
                }
                $res1=Model('contract')->data($contract)->allowField(true)->save();
                $data = ['cg_contract_id' => $contract_id, 'xs_contract_id' => $dataobj['dataobj']['xs_contract_id']];
                $result = Model('ComContract')->data($data)->save();
                if ($result && $res1) {
                    return json('success', '成功生成合同编号为' . $contract_id . '的合同！');
                } else {
                    return json('error', '添加失败!');
                }

        } else {
            return json('error', '请填写商品信息！');
        }
    }
}


    //直转合同数据
    public function zadd_data(){
        if(Request::instance()->isPost()){
            $data=input('param.formdata');
            $dataobj=json_decode($data,true);
            if($dataobj['goodsarr']==""){
                return json('error','请填写商品信息！');
            }
            if($dataobj['dataobj']){
                $str='CG';
                $c_contract_id=$this->create_id($str);
                $strs='XS';
                $x_contract_id=$this->create_id($strs);
                $xdata=array();
                $xdata['contract_id']=$c_contract_id;
                $xdata['business_name']=$dataobj['dataobj']['xname'];
                $xdata['money']=$dataobj['dataobj']['xmoney'];
                $xdata['dateofcollection']=$dataobj['dataobj']['xdateofcollection'];
                $xdata['remarks']=$dataobj['dataobj']['remarks'];
                $xdata['createdate']=date("Y-m-d H:i:s",time());
                $xdata['creator']=Session::get('name');
                $xdata['create_ip']=$_SERVER['REMOTE_ADDR'];
                $cdata=array();
                $cdata['contract_id']=$x_contract_id;
                $cdata['e_money']=$dataobj['dataobj']['emoney'];
                $cdata['money']=$dataobj['dataobj']['cmoney'];
                $cdata['business_name']=$dataobj['dataobj']['cname'];
                $cdata['dateofcollection']=$dataobj['dataobj']['cdateofcollection'];
                $cdata['remarks']=$dataobj['dataobj']['remarks'];
                $cdata['createdate']=date("Y-m-d H:i:s",time());
                $cdata['creator']=Session::get('name');
                $cdata['create_ip']=$_SERVER['REMOTE_ADDR'];
                $data=[0=>$xdata,1=>$cdata];
                $res=Model('contract')->saveAll($data,false);
                if($res){
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
            $page = input('param.page');
            $limit = input('param.limit');
            $start = ($page-1)*$limit;
            $where = [];
            $business_name=input('param.business_name');
            if($business_name){
                $where['business_name'] = $business_name;
            }
            $time=input('param.signtime');
            if($time){
                $time=explode(' - ',$time);
                $where["signtime"] = ["between",[$time[0],$time[1]]];
            }
            $contract_id = input('param.contract_id');
            if($contract_id){
                $where["contract_id"] = $contract_id;
            }
            $res = model("Contract")
                ->where(["contract_type"=>"c","task_id"=>""])
                ->limit($start.','.$limit)
                ->select();
            $count = model("Contract")
                ->where(["contract_type"=>"c"])
                ->count();
            return json_decode(json_encode(['code'=>0,'msg'=>'','count' =>$count,'data'=>$res],JSON_UNESCAPED_UNICODE));

        }
    }
    //销售合同列表数据
    public function contract_xdata(){
        if(request()->isPost()){
            $page = input('param.page');
            $limit = input('param.limit');
            $start = ($page-1)*$limit;
            $where = [];
            $business_name=input('param.business_name');
            if($business_name){
                $where['business_name'] = $business_name;
            }
            $time=input('param.signtime');
            if($time){
                $time=explode(' - ',$time);
                $where["signtime"] = ["between",[$time[0],$time[1]]];
            }
            $contract_id = input('param.contract_id');
            if($contract_id){
                $where["contract_id"] = $contract_id;
            }
            $res = model("Contract")
                ->where(["contract_type"=>"x","task_id"=>''])
                ->where($where)
                ->limit($start.','.$limit)
                ->select();
            $count = model("Contract")
                ->where(["contract_type"=>"x"])
                ->count();
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

    public function getContactDetail($contract_id){
        $contract_base = model("Contract")->get($contract_id);
        $contract_detail = model("ContractDetail")->getDetail($contract_id);
        $contract_base['content'] = $contract_detail;
        return $contract_base;
    }
}