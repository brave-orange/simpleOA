<?php
namespace app\index\controller;
use app\common\controller\CommonController;
use think\Request;
use think\Db;
use think\Session;
class Admin extends CommonController{
    public function menuList(){
        if (Request::instance()->isGet()){
            return view();
        }
    }

    public function roleList(){
        if (Request::instance()->isGet()){
            return view();
        }
    }
    public function userList(){
        if (Request::instance()->isGet()){
            return view();
        }
    }

    public function getUserList(){    //获取所有人员列表
        $res = model('Users')->field('id,name,phone,last_login')->select();
        $count = count($res);
        return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$res],JSON_UNESCAPED_UNICODE));
    }
    public function getRoleList(){    //获取所有人员列表
        $res = model('AuthRole')->field('id,name,remark')->select();
        $count = count($res);
        return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$res],JSON_UNESCAPED_UNICODE));
    }

    public function getMenuList(){    //获取所有菜单列表
        if (Request::instance()->isPost()){
           
            $res = model('Menu')
                //->limit($start,$limit)
                //->where(['is_menu'=>1])
                ->select();
            $menus =  get_column($res);
            foreach ($menus as  &$value) {
                if($value['level'] == 1){
                    $value['level'] = "二级菜单";
                    $value['menu_name'] ='____' . $value['menu_name'];
                }else if($value['level'] == 2){
                    $value['level'] = "三级操作";
                    $value['menu_name'] ='++++++' . $value['menu_name'];
                }else{
                    $value['level'] = "一级菜单";
                }
                
            }
            $count = count($menus);
            
            return json_decode(json_encode(['code'=>'0','msg'=>'','count' => $count,'data'=>$menus],JSON_UNESCAPED_UNICODE));
        }
    }
    public function addMenu(){        //添加菜单
        if (Request::instance()->isGet()){
            $pid = input('param.id');
            $this->assign('pid',$pid);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data = $_POST;
            //$data['is_menu'] = 1;
            if($data['pid']){     //添加的不是一级菜单，判断他是不是三级菜单
                $r = model('menu')->where(['id'=>$data['pid']])->field('pid')->find();
                $data['is_menu'] = $r['pid'] ? 0 : 1;     //0就是三级菜单，1就是二级菜单
            }
            if(model('menu')->insert($data)){
                return json('success','添加成功');
            }else{
                return json('error','添加失败');
            }

        }
    }
    public function addUser(){       //添加人员
        if (Request::instance()->isGet()){
            return view();
        }else if (Request::instance()->isPost()){
            $data = array();
            $data['id'] = input('post.id');
            $data['name'] = input('post.user_name');
            $data['phone'] = input('post.phone');
            $data['password'] = md6(input('post.password'));
            
            if(model('Users')->insert($data)){
                return json('success','添加成功');
            }else{
                return json('error','添加失败');
            }
        }
    }
    public function isRepeat(){
        if (Request::instance()->isPost()){
            $id  = input('post.id');
            if(model('Users')->get($id)){
                return json('error','ID已被注册');
            }else{
                return json('success','');
            }
        }
    }
    public function editMenu(){        //修改菜单
        if (Request::instance()->isGet()){
            $id = input('param.id');
            $level = input('param.level');
            $m = model('Menu')->where(['id'=>$id])->find();
            $where = ['pid'=>0,'is_menu'=>1];
            if($level == "三级操作"){
                $where['pid'] = ['neq',0];
            }
            $m_1 = model('Menu')->where($where)->select();  //所有级菜单
            $this->assign('data',$m);
            $this->assign('menus',$m_1);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data = $_POST;
            $data['is_menu'] = 1;

            $m = model('Menu')
                ->where(['id'=>$data['id']])
                ->find();
            $m['menu_name'] = $data['menu_name'];
            $m['action'] = $data['action'];
            $m['icon'] = $data['icon'];
            $m['pid'] = $data['pid'];
            $m['is_menu'] = $data['is_menu'];
            if($m->save()){
                return json('success','修改成功');
            }else{
                return json('error','修改失败');
            }

        }
    }
    public function editUser(){
        if (Request::instance()->isGet()){
            $id = input('get.id');
            $u = model('Users')->where(['id'=>$id])->find();
            $this->assign('data',$u);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data = array();
            $id = input('post.id');
            $data['name'] = input('post.user_name');
            $data['phone'] = input('post.phone');
            $data['password'] = md6(input('post.password'));
            $u = model('Users')
                ->save($data,['id'=>$id]);
            if($u){
                return json('success','修改成功');
            }else{
                return json('error','修改失败');
            }
        }
    }
    public function delMenu(){        //删除菜单
        if (Request::instance()->isPost()){
            $id = input('param.id');
            $data=$this->deldegui($id);
           if($data){
               return json('error','删除失败请先删除子菜单！');
           }
            $m = model('Menu')
                ->where(['id'=>$id])
                ->find();
            if($m->delete()){
                return json('success','删除成功');
            }else{
                return json('error','删除失败');
            }

        }
    }
//删除递归
    public function deldegui($id,$del=array()){
        $z=Db::name('menu')
            ->where(['pid'=>$id])
            ->select();
        if($z!=NULL){
            $del=[$id=>$z];
            foreach($z as $key=>$value){
               $this->deldegui($value['id'],$del);
            }
        }
            return $del;
    }
    public function delUser(){  //删除用户
        if (Request::instance()->isPost()){
            $id = input('param.id');

            $m = model('Users')
                ->where(['id'=>$id])
                ->find();
            if($m->delete()){
                return json('success','删除成功');
            }else{
                return json('error','删除失败');
            }

        }
    }
    public function giveRole(){   //给用户分配角色
        if (Request::instance()->isGet()){
            $id = input('get.id');
            $data = model('AuthRole')->getRoleList($id);
            $this->assign('uid',$id);
            $this->assign('data',$data);
            return $this->fetch();
        }else if(Request::instance()->isPost()){   //为用户分配角色
            $id = input('post.uid');
            $roles = $_POST['roles'];
            if(!empty($roles)){
                model("AuthAccess")->where(['uid'=>$id])->delete();//删除用户原有角色
                foreach ($roles as $key => $value) {
                    model("AuthAccess")->addAccess($id,$value);   //用户新增角色
                }
            }else{
                model("AuthAccess")->where(['uid'=>$id])->delete();//删除用户原有角色
            }
            
             return json('success','操作成功',$roles);
        }
    }

    public function addRole(){  //添加角色
        if (Request::instance()->isGet()){
            return view();
        }else if (Request::instance()->isPost()){
            $data = array();
            $data['name'] = input('post.name');
            $data['remark'] = input('post.remark');
            $data['createtime'] = date('Y-m-d H:i:s');
            $data['status'] = 1;
            if(model('AuthRole')->addRole($data)){
                return json('success','创建成功');
            }else{
                return json('error','创建失败');
            }

        }
    }

    public function editRole(){
        if (Request::instance()->isGet()){
            $id = input('get.id');
            $data = model('AuthRole')->get($id);
            $this->assign('data',$data);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $data = array();
            $id = input('post.id');
            $data['name'] = input('post.name');
            $data['remark'] = input('post.remark');
            $r = model('AuthRole')
                ->save($data,['id'=>$id]);
            if($r){
                return json('success','修改成功');
            }else{
                return json('error','修改失败');
            }

        }
    }
     public function delRole(){
        if (Request::instance()->isPost()){
            $id = input('param.id');
            $m = model('AuthRole')
                ->where(['id'=>$id])
                ->find();
            if($m->delete()){
                return json('success','删除成功');
            }else{
                return json('error','删除失败');
            }
        }
     }

     public function roleRule(){   //查看角色权限列表
        if (Request::instance()->isGet()){
            $role_id = input('param.role_id');
            $rules = model('AuthRole')->getRulesByRole($role_id);
            $menus = get_column($rules,1);        //权限打散成数组
            foreach ($menus as  &$value) {
                if($value['level'] == 1){
                    $value['level'] = "二级菜单";
                    $value['menu_name'] ='____' . $value['menu_name'];
                }else if($value['level'] == 2){
                    $value['level'] = "三级操作";
                    $value['menu_name'] ='____+++' . $value['menu_name'];
                }else{
                    $value['level'] = "一级菜单";
                }
            }
            $this->assign('rules',$menus);
            return view();
        }else if (Request::instance()->isPost()){

        }
     }

     public function giveRules(){
        if (Request::instance()->isGet()){
            $role_id = input('get.role_id');
            $res = model('Menu')
                //->where(['is_menu'=>1])
                ->select();
            $menus_arr = model('AuthRole')->getRules($role_id);
            
            foreach ($res as &$value) {
                if(in_array($value['id'], $menus_arr)){
                    $value['have'] = 1;
                }else{
                    $value['have'] = 0;
                }
            }
            $rules = get_column($res,2);

            $this->assign('role_id',$role_id);
            $this->assign('rules',$rules);
            return $this->fetch();
        }else if (Request::instance()->isPost()){
            $role_id = input('post.role_id');
            $rules = $_POST['rules'];
            $res = model("AuthRole")->setRule($role_id,$rules);
            if($res){
                return json('success','操作成功');
            }else{
                return json('error','操作失败');
            }
           
        }
     }

}