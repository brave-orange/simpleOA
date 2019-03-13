<?php


namespace app\index\controller;

use think\Controller;
use think\Request; 
class Error extends Controller
{

    public function permissionDenied(){   //没有权限错误
        if (Request::instance()->isGet()){
            return view();
        }else if (Request::instance()->isPost()){
            return json('error','您没有权限进行此操作~');
        }
    }
    public function permissionDeniedPost(){   //没有权限错误
        
        return json('error','您没有权限进行此操作~');
        
    }

}
