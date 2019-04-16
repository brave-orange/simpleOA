<?php
namespace app\index\model;
use think\Model;

class ProcessNode extends Model{
    protected $table="coa_process_node";
    public function insert($data){
         if($data){
            if($this->isUpdate(false)->save($data)){
                return true;
            }
        }
        return false;
    }
    public function getAllnodes($head_id){//查出整个流程的节点排序

    }
    public function getNextNode($node_id){
        return $this->get($this->get($node_id)["next_node"]);
    }
    public function getFristNode($process_id){
        return $this->where(["processid"=>$process_id,"ishead"=>1])->find();
    }

    public function savelist($data){
        $error = array();
        foreach ($data as $value) {
            if(!$this->insert($value)){
                $error[] = $value;
            }
        }
        if(count($error)){
            return json("error","部分数据存储失败！",$error);
        }else{
            return json("success","存储成功!");
        }
    }

    public function deleteProcessNodes($processid){
        return $this->where(['processid'=>$processid])->delete();
    }
    public function isLastNode($nodeid){
        $node = $this->get($nodeid);
        if(!$node){
            return true;
        }
        return false;
    }
}