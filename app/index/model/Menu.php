<?php
namespace app\index\model;
use think\Model;

class Menu extends Model{
    protected $table="menu";

    public function getMenus($ids){
        $menus = $this->where(['id'=>['IN',$ids],'is_menu'=>1])->select();
        return $menus;
    }
    public function insert($data){
         if($data){
            if($this->save($data)){
                return true;
            }
        }
        return false;
    }
}
