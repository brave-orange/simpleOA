<?php

/***
权限模型
***/
namespace app\index\model;

use think\Config;
use think\Db;
use think\Loader;
use think\Model;

class AuthAccess extends Model{
    protected $table = 'auth_access';

    private function getUserMenuArr($uid){
        $rules = $this->alias('a')
            ->join('auth_role b','a.role_id = b.id')
            ->where(['a.uid'=>$uid,'b.status'=>1])
            ->field('b.rules')
            ->select();
        $menus_str = "";
        if(!$rules){
            return array();
        }
        foreach ($rules as $key => $value) {
            $menus_str .= $value['rules']. ',';
        }
        $menus_str = rtrim($menus_str, ',');  //移除最右侧的逗号
        $rules_arr = array_unique(explode(',', $menus_str));//以逗号分开成数组，并且移除重复的
        return $rules_arr;
    }
    public function getUserMenu($uid){   //获取用户的可访问的菜单
        $rules_arr = $this->getUserMenuArr($uid);
        return model('menu')->getmenus($rules_arr);     //获取菜单对象

    }

    //获取用户角色列表
    public function getUserRole($uid){
        $res = $this->field('role_id')->where(['uid'=>$uid])->select();
        $list = [];
        foreach ($res as $key => $value) {
            $list[] = $value['role_id'];
        }
        return $list;
    }
    //为用户添加角色
    public function addAccess($uid,$role_id){
        $this->data([
            "role_id" => $role_id,
            "uid" => $uid
        ]);
       return $this->save();
    }

    //删除角色权限规则
    public function delAccess($uid,$role_id){
        if($this->where(array("role_id"=>$role_id,"uid"=>$uid))->delete()){
            return true;
        }else{
            return false;
        }
    }

    //根据用户id和权限(菜单)id查询用户是否有某个权限
    public function checkUserRulebyID($uid,$rule_id){
        $rules = $this->getUserMenu($uid);   //获取权限列表
        if(in_array($rule_id, $rules)){
            return true;
        }else{
            return false;
        }
    }

    //根据用户id和权限规则名(菜单名)查询用户是否有某个权限
    public function checkUserRule($uid,$rule_val){
        $menu_id = model("menu")
            ->where(['action'=>$rule_val])
            ->field('id')
            ->find()['id'];
        $rules = $this->getUserMenuArr($uid);
        if($menu_id == ''){
            return true;
        }
        if(in_array($menu_id,$rules)){
            return true;
        }else{
            return false;
        }
    }

}   
