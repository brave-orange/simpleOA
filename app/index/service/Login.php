<?php
namespace app\index\service;

use think\Model;
use think\Request;
use think\Session;
Class Login extends Model
{
    //登录操作
    public function dologin(){
        
        
        $uid = input('param.name');
        $password = input('param.pwd');
        $user = model('Users')
             ->where(['id'=>$uid])
             ->find();
        if(md6($password) == $user['password']){
            Session::set('uid',$uid);
            Session::set('name',$user['name']);
            $user['last_login'] = date('Y-m-d H:i:s');
            return true;
        }else{
            return false;
        }
    }
}

