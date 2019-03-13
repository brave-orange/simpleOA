<?php


namespace app\common\controller;

use think\Controller;
use think\Request; 
use think\Session;
use app\index\model\AuthAccess;
class CommonController extends Controller
{
    public function _initialize()
    {
        if (Request::instance()->isGet()){
            $uid = Session::get('uid');
            if(NULL == $uid) {
                $this->redirect('/login');
                //没登陆，跳转到登陆页
            }else{
                if(!$this->check($uid)){
                    $this->redirect('/permission_denied');
                 }
            }
        }else if (Request::instance()->isPost()){
            $uid = Session::get('uid');
            if(NULL == $uid) {             
                $this->error(["code"=>0,"msg"=>"未登录状态无法调用！"]);

            }else{
                 if(!$this->check($uid)){
                    $this->redirect('/permission_denied_post');
                 }
            }
        }

    }

    private function check($uid){
        $addr = $_SERVER["REQUEST_URI"];    //域名后的请求地址
        $addr = trim($addr,'/');
        if(stripos($addr,".") != 0){
            $addr =  substr($addr,0,0-(strlen($addr)-stripos($addr,".")));
        }
        if($addr != ''){
            $auth = new AuthAccess();
            return $auth->checkUserRule($uid,$addr);
        }
        return true;
    }

}
