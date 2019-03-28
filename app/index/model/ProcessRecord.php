<?php
namespace app\index\model;
use think\Model;

class ProcessRecord extends Model{
    protected $table="coa_process_record";
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }


}