<?php


namespace app\index\model;

use think\Config;
use think\Db;
use think\Loader;
use think\Model;
use think\Session;
class AuthRole extends Model{
    protected $table = 'auth_role';
    


    //查询角色的下辖用户
    public function getRoleUser($role_id){
        $users = model("User")->where(array("Role_id"=>array("like","%".$role_id."%")))->select();
        return $users;
    }

    public function getRoleList($uid){
        
        $res = $this->field('id,name')->select();
        $mylist = model('AuthAccess')->getUserRole($uid);
        foreach ($res as $key => &$value) {
            if(in_array($value['id'],$mylist)){
                $value['have'] = 1;
            }else{
                $value['have'] = 0;
            }
        }
        return $res;
    }


    public function getRulesByRole($role_id){    //获取角色的权限列表
        $rule_str =  $this->where(['id'=>$role_id])->field('rules')->find()['rules'];
        $rule_str = rtrim($rule_str, ',');  //移除最右侧的逗号
        $rules_arr = array_unique(explode(',', $rule_str));//以逗号分开成数组，并且移除重复的
        return model('Menu')->where(['id'=>['IN',$rules_arr]])->select();     //获取菜单对象
    }
    public function getRules($role_id){    //获取角色的权限列表
        $rule_str =  $this->where(['id'=>$role_id])->field('rules')->find()['rules'];
        $rule_str = rtrim($rule_str, ',');  //移除最右侧的逗号
        $rules_arr = array_unique(explode(',', $rule_str));//以逗号分开成数组，并且移除重复的
        return $rules_arr;    //获取菜单对象
    }

    //增加角色
    public function addRole($role){
        if($this->data($role)->save()){
            return true;
        }else{
            return false;
        }   
    }
    
    //查询角色名是否重复
    public function getname($name){
        if($this->where(array('name'=>$name))->find()){
            return false;
        }else{
            return true;
        }
    }

    //删除角色
    public function delRole($role_id){
        if($this->where(array('id'=>$role_id))->delete()){
            return true;
        }else{
            return false;
        }
    }

    //更改角色状态
    public function setStatus($role_id,$status){
        if(in_array($status,[1,0])){
            if($this->save(['status'=>$status],['id'=>$role_id])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }

    //修改角色
    public function updateRole($role_id,$role){
        if($this->save($role,["id"=>$role_id])){
            return true;
        }else{
            return false;
        }
    }

    //设置角色权限 
    public function setRule($role_id,$rules){
        $rules_str = "";
        foreach ($rules as $value) {
                $rules_str = $rules_str . ',' . $value;
        }
        $rules_str = trim($rules_str, ',');
        $res =  $this->save([
            'rules'=>$rules_str],['id'=>$role_id]);
        return $res;
    }
}
