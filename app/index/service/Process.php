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


    public function startProcess($process_id,$content,$remark,$stamp){           //审批流程开始       
        $count = model("ProcessTask")->count();
        $data = [];
        $data["task_id"]  = "TS-".date("Ymd")."-".($count+1);
        $data["process_id"] = $process_id;
        $data["content"] = $content;
        $data["create_date"] = date("Y-m-d H:i:s");
        $data["create_person"] = Session::get("name");
        $data["status"] = model("ProcessNode")->getFristNode($process_id)["node_name"];//获取头结点
        $data["iscomplete"] = 0;   //初始化审批任务状态为未完成
        $data["stamp"] = $stamp;
        if(model("ProcessTask")->insert($data)){  //初始化第一步(添加提交审批的审批记录)
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
            $record["remark"] = $remark;
            $nextnode = model("ProcessNode")->getNextNode($record["node_id"]);
            $task_next = $this->nextNode( $record["id"],$record["task_id"],$nextnode);//进入下一个审批节点，添加任务
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

    public function getMyTask(){
        $uid = Session::get("uid");
        $roles = model("AuthAccess")->getUserRole($uid);
        $res = model("TaskCache")->where(['role'=>['In',$roles]])->select();
        return $res;
    }
    //找指定的待审批任务信息
    public function getTaskinfo($start,$limit,$type="",$personal=false){//$type:查找的流程类型,$personal:是否找个人的待审批任务
    //发起人，合同编号，供应商名称，总价，当前流程，当前处理人
        $where = [];
        if($personal){
            $roles = model("AuthAccess")->getUserRole(Session::get("uid"));
            $where["a.role"] = ['In',$roles];
        }
        if($type != ""){

            $where["c.process_id"]=$type;
        }
        $res = model("TaskCache")->alias("a")
            ->join("coa_process_record b",'a.last_record_id = b.id')
            ->join("coa_process_task c",'a.task_id = c.task_id')
            ->join("coa_contract d",'a.task_id = d.task_id')
            ->field("a.id,c.task_id,b.opera_person last_person,c.create_person,c.status,d.contract_id,d.business_name,d.money,d.signtime")
            ->where($where)
            ->limit($start.','.$limit)
            ->select();
        $count = model("TaskCache")->alias("a")
            ->join("coa_process_record b",'a.last_record_id = b.id')
            ->join("coa_process_task c",'a.task_id = c.task_id')
            ->join("coa_contract d",'a.task_id = d.task_id')
            ->where($where)
            ->count();
        return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$res],JSON_UNESCAPED_UNICODE));
    }
    public function nodePass(){

    }

    public function getTaskInfoByCache($cid){
        $cache = model("TaskCache")->get($cid);
        $task = model("ProcessTask")->get($cache['task_id']);
        $action = model("Process")->get($task['process_id']);
        return ["process_id"=>$task['process_id'],"content"=>$task['content'],"action"=>$action['action']];
    }

    public function getTaskHistory($task_id){   //得到当前审批任务的审批历史
         return model("ProcessRecord")->alias("a")
            ->join("coa_process_node b","a.node_id = b.nodeid")
            ->where(['task_id'=>$task_id])
            ->field("a.id,a.status,a.opera_time,a.opera_person,a.remark,b.node_name")
            ->order("opera_time asc")->select();

    }

}