<?php
namespace app\index\service;

use think\Model;
use think\Request;
use think\Session;
Class Process extends Model{
    public function insert_node($data){    //插入节点
        $count = model('ProcessNode')->count();
        $data['node_id'] = "ND-".date("Ymd")."-".($count+1);
        $nextnode = model('ProcessNode')->get($data['next_node']);    //待插入节点的下级节点
        $lastnode = model('ProcessNode')->where(['next_node' => $targetnode['nodeid']])->find();  //待插入节点的上级节点
        if($data['next_node']){
            if($nextnode['ishead']){     //新插入的节点变为头结点
                $data['ishead'] = 1;
                $nextnode = 0;
            }else{
                $data['ishead'] = 0;
            }
            $lastnode['next_node'] = $data['node_id'];
            $lastnode->save();
        }else{
            $finalnode = model('ProcessNode')->where(['next_node' => ""])->find();  //流程尾部节点
            $finalnode['next_node'] = $data['node_id'];
        }
        $r = model('ProcessNode')->insert($data);
        if(!$r){
            return json('error','出现问题了！');
        }else{
            $nextnode->save();
            $lastnode->save();
            $finalnode->save();
            return json('success','添加成功');
        }
    }

    public function add_process(){   //新增流程
        $count = model('Process')->count();
        $data['process_id'] = "PR-".date("Ymd")."-".($count+1);
        if(model('Process')->insert($data)){
            return json('success','添加成功');
        }else{
            return json('error','出现问题了！');
        }
    }

    public function init_process($data_str){   //初始化流程节点
        $data = json_decode($data_str);
        $count = model('ProcessNode')->count();
        foreach ($data as &$value) {
            $value['node_id'] = "ND-".date("Ymd")."-".(++$count);
        }
        foreach ($data as $key=>&$value) {    //建好节点顺序
            if($key == 0){
                $value['ishead'] = 1;
            }else{
                $value['ishead'] = 0;
            }
            if($key == count($data)-1){
                $value['next_node'] = "";
            }else{
                $value['next_node'] = $data[$key+1]['nodeid'];
            }      
        }
        $res = model("ProcessNode")->savelist($data);



    }

    public function get_process_list($start,$limit,$status){    //获取所有流程
        $data = model('process')
            ->where(['status'=>$status])
            ->limit($start,$limit)
            ->select();
        $count = model('process')->where(['status'=>$status])->count();
        return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$data],JSON_UNESCAPED_UNICODE));

    }

}