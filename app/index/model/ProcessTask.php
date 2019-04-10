<?php
namespace app\index\model;
use think\Model;

class ProcessTask extends Model{
    protected $table = "coa_process_task";
    public function insert($data){
         if($data){
            if($this->isUpdate(false)->save($data)){
                return true;
            }
        }
        return false;
    }
}