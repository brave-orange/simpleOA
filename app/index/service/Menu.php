<?php
namespace app\index\service;

use think\Model;
use think\Request;
use think\Session;
Class Menu extends Model
{
    //登录操作
    public function getUserMenu(){     //获取用户的权限能用的菜单
        $uid = Session::get('uid');
        model('Auth','service')->getRules();

    }
}