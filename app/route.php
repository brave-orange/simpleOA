<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'main'                  =>      'index/index/main'
    ,'permission_denied'    =>      'index/Error/permissionDenied'
    ,'permission_denied_post'  =>      'index/Error/permissionDeniedPost'
    ,'login'                =>      'index/login/login'
    ,'addpurchase'          =>      'index/Purchase/addPurchase'
    ,'purchaselist'         =>      'index/Purchase/purchaselist'
    ,'waitpurchaselist'     =>      'index/Purchase/waitpurchaselist'
    ,'purchasedList'        =>      'index/Purchase/purchasedList'
    ,'addyinbiao'           =>      'index/Yinbiaocard/addyinbiao'
    ,'upload'               =>      'index/Yinbiaocard/upload'
    ,'contract'             =>      'index/contract/contract'
    ,'customer_list'        =>      'index/customer/customer_list'
    ,'contract_sel'         =>      'index/contract/contract_sel'
    ,'cardrecharge'         =>      'index/Yinbiaocard/cardrecharge'
    ,'uploadrecharge'       =>      'index/Yinbiaocard/uploadrecharge'
    ,'contract_npass'       =>      'index/contract/contract_no_pass'
    ,'contract_pass'        =>      'index/contract/contract_pass'
    ,'customer_info'        =>      'index/customer/customer_info'
    ,'channel_info'         =>      'index/customer/channel_info'
    ,'channel_list'         =>      'index/customer/channel_list'
    ,'menulist'             =>      'index/admin/menulist'
    ,'rolelist'             =>      'index/admin/rolelist'
    ,'userlist'             =>      'index/admin/userlist'
    ,'pay_log'              =>      'index/contract/pay_log'
    ,'supplier_list'        =>      'index/purchase/supplier_list'
    ,'receive_card'         =>      'index/Yinbiaocard/receiveCard'
    ,'verify_receive'       =>      'index/Yinbiaocard/verifyReceive'
    ,'my_card'              =>      'index/Yinbiaocard/myCard'





];
