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

}