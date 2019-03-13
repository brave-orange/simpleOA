<?php

// 应用公共文件
// 
use lib\api_demo\sendFilePsw;
use lib\api_demo\sendFail;
function json($status,$msg="",$data=array()){
  $result=array(
   'status'=>$status,
   'msg'=>$msg,
   'data'=>$data
  );
  //输出json
  return json_encode($result,JSON_UNESCAPED_UNICODE);
  exit;
}       //json格式化输出方法
function create_token($token_len){   //生成随机token
    $str='yc7r1ambwjkxz6p8dqelnvos45f03uigh2t9';   //密码种子
    $str = str_shuffle($str);    //打乱字符串
    $len=strlen($str)-1;
    $randstr='';
    for($i=0;$i<$token_len;$i++){
        $num=rand(0,$len);
        $randstr .= $str[$num];    //随机顺序组成随机密码
    }
    return $randstr;
}

function md6($password){
    $key = "chuanzehahah";
    $m = md5($password).md5($key);
    $password = md5($m);
    return $password;    
  
}    //密码加密


function card_error_log($card_no,$msg = null){   //卡号存储错误计入日志
    $masg = "";
    for($i = 0 ; $i < count($card_no) ; $i++){
        $masg = '['.date("Y-m-d H:i:s").']'.'卡号：'.json_encode($card_no[$i]).$msg;

    }
            // 日志文件名：日期.txt
    $path = RUNTIME_PATH.DS.'cardNo_log'. DS .date("Ymd").'.txt';
    file_put_contents($path, $masg.PHP_EOL,FILE_APPEND);
}

function financial_log($data,$msg = null){   //存储财务审核日志
    $masg = '['.date("Y-m-d H:i:s").']        ';
    foreach ($data as $key => $value) {
        $masg .= $key.':'.$value."  |  ";
    }
            // 日志文件名：日期.txt
    $path = RUNTIME_PATH.DS.'log'. DS .'financial_log.txt';
    file_put_contents($path, $masg.PHP_EOL,FILE_APPEND);
}

  /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }
//递归父子级菜单
function get_conlumns($array){
    if($array!=NULL){
echo 1231;
    }
}
//递归父子级菜单    type:1:顺序菜单 2树状菜单
function get_column($array,$type=1,$fid=0,$level=0)
{
    $column = [];
    if($type == 2)
        foreach($array as $key => $vo){
        if($vo['pid'] == $fid){
            $vo['level'] = $level;
            $column[$key] = $vo;
            $column [$key][$vo['id']] = get_column($array,$type=2,$vo['id'],$level+1);
        }
    }else{
        foreach($array as $key => $vo){
            if($vo['pid'] == $fid){
                $vo['level'] = $level;
                $column[] = $vo;
                $column = array_merge($column, get_column($array,$type=1,$vo['id'],$level+1));
            }
        }
    }
    
    return $column;
}

function qian($num,$last = false){
    $digit = ['零', '壹', '贰', '叁', '肆',
        '伍', '陆', '柒', '捌', '玖'];
        $unit = ['','拾','佰','仟'];
    $daxie = "";
    if($num == 0){
        return '';
    }
    for ($i=3; $i>=0 ; $i-- ) {
        if($num%(pow(10,$i)) == 0){              //遇到整数直接跳出循环
            $daxie = $daxie.$digit[$num/pow(10,$i)].$unit[$i];
            break;
        }
        if($num<(pow(10,$i))){           //碰到有一个零的情况下判断（要判断的数比要判断的权值要小），如果上一个还是零，则不再加一个零了
            if(substr($daxie, -3) != $digit[0]){
                $daxie = $daxie.$digit[$num/pow(10,$i)];
            }
        }else{
            if($num/(pow(10,$i)) > 0){    //正常情况加一个大写数
                $daxie = $daxie.$digit[$num/pow(10,$i)].$unit[$i];
            }
        }
        $num = $num%(pow(10,$i));
    } 
    if($last && substr($daxie, 0, 3) == $digit[0]){    //最前面的四位数前面不能再读一个零了
        $daxie = substr($daxie, 3);
    }
    return $daxie;
}

function number_to_big($number,$level=0){   //每千位递归
   
    $big_unit = ['','万','亿','万亿'];

    
    $str_number = (string)$number;
    if(strlen($str_number)>4){
        $str = substr($str_number,0,-4);
        $set = substr($str_number,-4);
        $num = (int)$set;
        $daxie =  qian($num);
        if($daxie != ""){        //防止出现一亿万圆整这样的错误
            $a = $daxie.$big_unit[$level];
        }else{
            $a = $daxie;
        }
        return  number_to_big((int)$str,$level+1).$a;     //递归
        
    }else{
        return qian($number,true).$big_unit[$level];;

    }
   
   
}

function number_transf($number){   //数字转换大写
    $y=explode(".",$number);
    $digit = ['零', '壹', '贰', '叁', '肆',
        '伍', '陆', '柒', '捌', '玖'];
    $decimal = "";
    $integer = number_to_big((float)$y[0]).'圆';
    if(isset($y[1])){
        if($y[1] == '00'){
        $decimal = "整";
    }else{        //小数位
        if(substr($y[1],-1)!=0){
            $decimal = $digit[(int)substr($y[1],-1)].'分';
        }
        if(substr($y[1],0,1)!=0){
            $decimal = $digit[substr($y[1],0,1)].'角'.$decimal;
        }
    }
    }else{
        $decimal = "整";
    }
    
    return $integer.$decimal;
    

}

