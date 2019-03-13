<?php
namespace app\index\controller;
use think\Controller;
use think\Request; 
use think\Db;
use think\Session;
use think\captcha\Captcha;
class Login extends Controller
{
    public function login(){

        return view();
    }
    public function verify()
    {

        $config =    [
            // 验证码字体大小
            'fontSize'    =>    120,    
            // 验证码位数
            'length'      =>    4,   
            // 关闭验证码杂点
            'useNoise'    =>    true, 
            'useCurve' => false
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    public function dologin(){    //登录验证
        $captcha = new Captcha();
        $code = input('param.captcha');
        if(!$captcha->check($code)){
            return json('error','验证码错误');
        }

        if(model('Login','service')->dologin()){

            return json('success','登陆成功');
        }else{
            return json('error','账号或密码错误');
        }
    }

    public function logout(){
        Session::delete('uid');
        Session::delete('name');
        $this->redirect('/');
    }
}