<?php
namespace app\index\controller;
use app\common\controller\CommonController;
use think\Controller;
use think\Request; 
use think\Db;
use think\Session;
class Index extends CommonController
{
    public function index()
    {
        $menus_arr = model('Auth','service')->getUserMenus();
        $menus = get_column($menus_arr,2);

        $this->assign('menus',$menus);
        return $this->fetch();

    }
    public function main(){
        return "稍等、、、";
    }

    public function tool1(){
        if (Request::instance()->isGet()){
            return view();
        }else if (Request::instance()->isPost()){
            $number = input('param.number');
            $str = number_transf($number);
            return json('success',$str);
        }
    }




}
