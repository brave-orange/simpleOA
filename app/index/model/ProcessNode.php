<?php
namespace app\index\model;
use think\Model;

class ProcessNode extends Model{
    protected $table="coa_process_node";
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }
    public function getAllnodes($head_id){//查出整个流程的节点排序

    }

    public function savelist($data){
        if($data){
            if($this->saveAll($data)){
                return true;
            }
        }
        return false;
    }
}