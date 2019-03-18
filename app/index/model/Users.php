<?php
namespace app\index\model;
use think\Model;

class Users extends Model{
    protected $table="coa_auth_users";
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }

    

}
