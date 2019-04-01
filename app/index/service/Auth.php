<?php
namespace app\index\service;

use think\Model;
use think\Request;
use think\Session;
Class Auth extends Model
{
    //权限操作
    public function getUserMenus($uid = 0){
        if($uid == 0){
            $uid = Session::get('uid');
        }
        return model('AuthAccess')->getUserMenu($uid);
    }
    //分配角色
    public function giveUserRole($userid,$roleid){
        return model('AuthAccess')->addAccess($uid,$role_id);
    }
    public function getAllroles(){
        return model("AuthRole")->select();
    }
}