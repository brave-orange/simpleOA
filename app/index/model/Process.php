<?php
namespace app\index\model;
use think\Model;

class Process extends Model{
    protected $table="coa_process";
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }
    public function getAllProcess(){
        return $this->select();
    }
    public function getCreateTimeAttr($time)
    {
        return $time;//返回create_time原始数据，不进行时间戳转换。
    }
    public function del_process($process_id){
        return $this->destroy($process_id);
    }
    
}