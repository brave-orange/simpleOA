<?php
/**
 * 任务缓存模型
 * wyc
 */
namespace app\index\model;
use think\Model;

class TaskCache extends Model{
    protected $table="coa_task_cache";
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }
}