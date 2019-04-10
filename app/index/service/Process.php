<?php
namespace app\index\service;

use think\Model;
use think\Request;
use think\Session;
Class Process extends Model{
    public function insert_node($data_str){    //插入节点
        $data = json_decode($data_str,true);
        $count = model('ProcessNode')->count();
        $data['nodeid'] = "ND-".date("Ymd")."-".($count+1);
        $nextnode = model('ProcessNode')->get($data['next_node']);    //待插入节点的下级节点
        $lastnode = model('ProcessNode')->where(['next_node' => $nextnode['nodeid']])->find();  //待插入节点的上级节点
        if($data['next_node']){
            if($nextnode['ishead']){     //新插入的节点变为头结点
                $data['ishead'] = 1;
                $nextnode['ishead']  = 0;
            }else{
                $data['ishead'] = 0;
                $lastnode['next_node'] = $data["nodeid"];
                $lastnode->save();
            }

        }else{
            $finalnode = model('ProcessNode')->where(['next_node' => ""])->find();  //流程尾部节点
            $finalnode['next_node'] = $data['nodeid'];
            $finalnode->save();
        }
        $r = model('ProcessNode')->insert($data);
        if(!$r){
            return false;
        }else{
            $nextnode->save();
            
            return $data;
        }
    }

    public function add_process($data){   //新增流程
        $count = model('Process')->where("create_time",">",date("Y-m-d 00:00:00"))->count();
        $data['process_id'] = "PR-".date("Ymd")."-".($count+1);
        if(model('Process')->insert($data)){
            return json('success','添加成功');
        }else{
            return json('error','出现问题了！');
        }
    }

    public function init_process($data_str){   //初始化流程节点
        $data = json_decode($data_str,true);
        $count = model('ProcessNode')->where("create_date",">",date("Y-m-d 00:00:00"))->count();

        foreach ($data as &$value) {   //生成ID
            $value['nodeid'] = "ND-".date("Ymd")."-".(++$count);
            $value['create_date'] = date("Y-m-d");
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
        if($res){
            $p = model("Process")->where(["process_id"=>$data[0]["processid"]])->find();
            $p["status"] = 1;
            $p->save();
        }
        return $res;
    }

    public function del_process($pid){
        $ststus = model("Process")->where(["process_id"=>$pid])->find();
        if($status){
            return model("Process")->del_process($pid);
        }else{
            if(model("Process")->del_process($pid)){
                return model("ProcessNode")->deleteProcessNodes($pid);
            }else{
                return false;
            }
            
        }
        
    }

    public function get_process_list($start,$limit,$status){    //获取所有流程
        $data = model('process')
            ->where(['status'=>$status])
            ->limit($start.','.$limit)
            ->select();
        $count = model('process')->where(['status'=>$status])->count();
        return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$data],JSON_UNESCAPED_UNICODE));
    }

    public function get_process_nodes($process_id){  //获取流程的所有节点
        $nodes = [];
        $res = model("ProcessNode")->where(["processid"=>$process_id,"ishead"=>1])->find();
        $res["opera_person_name"] = model("AuthRole")->where(['id'=>$res["opera_person"]])->find()["name"];
        $nodes[] = $res;
        do{
            $res = model("ProcessNode")->where(["nodeid"=>$res["next_node"]])->find();
            $res["opera_person_name"] = model("AuthRole")->where(['id'=>$res["opera_person"]])->find()["name"];
            $nodes[] = $res;

        }while ($res["next_node"] != "");
        
        return $nodes;
    }

    //进入下一个审批节点（作为生产者，把下一个要执行的节点任务写入任务缓存的表里等待消费）
    public function nextNode($last_record_id,$task_id,$nextnode){
        $data = ["last_record_id"=>$last_record_id,"task_id"=>$task_id,"current_node_id"=>$nextnode["nodeid"],"role"=>$nextnode["opera_person"]];
        return model("TaskCache")->insert($data);
        
    }


    public function startProcess($process_id,$content){           //审批流程开始       
        $count = model("ProcessTask")->count();
        $data = [];
        $data["task_id"]  = "TS-".date("Ymd")."-".($count+1);
        $data["process_id"] = $process_id;
        $data["content"] = $content;
        $data["create_date"] = date("Y-m-d H:i:s");
        $data["create_person"] = Session::get("name");
        $data["status"] = model("ProcessNode")->getFristNode($process_id)["node_name"];//获取头结点
        $data["iscomplete"] = 0;   //初始化审批任务状态为未完成
        if(model("ProcessTask")->insert($data)){  //初始化第一步
            $record = [];
            $record["task_id"] = $data["task_id"];
            $count = model("ProcessRecord")->where(["task_id"=>$data["task_id"]])->count();
            $record["id"] = $record["task_id"]."-NO".($count+1);
            $record["process_id"] = $data["process_id"];
            $record["node_id"] = model("ProcessNode")->getFristNode($process_id)["nodeid"];
            $record["status"] = "pass";
            $record["opera_person"] = Session::get('name');
            $record["opera_ip"] = $_SERVER['REMOTE_ADDR'];
            $record["opera_time"] = date("Y-m-d H:i:s");
            $record["remark"] = "";
            $nextnode = model("ProcessNode")->getNextNode($record["node_id"]);
            $task_next = $this->nextNode( $record["id"],$record["task_id"],$nextnode);
            unset($data);
            if($task_next){
                model("ProcessRecord")->insert($record);
                return $record["task_id"];
            }else{
                return false;
            }
           
        }
        return false;
    }

}