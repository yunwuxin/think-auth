<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

//think-auth 配置文件
return [
    'provider' => [
        'type'  => 'model',
        'model' => \yunwuxin\auth\model\User::class
    ],
    'guard'    => 'session',
    //设为false,则不注册路由
    'route'    => [
        'group'       => 'auth',
        'controllers' => [
            'login'    => \yunwuxin\auth\controller\LoginController::class,
            'register' => \yunwuxin\auth\controller\RegisterController::class,
            'forgot'   => \yunwuxin\auth\controller\ForgotPasswordController::class,
            'reset'    => \yunwuxin\auth\controller\ResetPasswordController::class
        ]
    ]
];