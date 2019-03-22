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
    'main'                      =>      'index/index/main'
    ,'permission_denied'        =>      'index/Error/permissionDenied'
    ,'permission_denied_post'   =>      'index/Error/permissionDeniedPost'
    ,'login'                    =>      'index/login/login'
    ,'logout'                   =>      'index/login/logout'
    ,'menulist'                 =>      'index/admin/menulist'
    ,'rolelist'                 =>      'index/admin/rolelist'
    ,'userlist'                 =>      'index/admin/userlist'
];
