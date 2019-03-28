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


}