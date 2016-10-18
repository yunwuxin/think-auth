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
    /**
     * 用户对象提供者，需实现 \think\auth\interfaces\Authenticatable
     */
    'provider'           => \think\auth\model\User::class,

    /**
     * 登录控制器
     */
    'controller'         => \think\auth\controller\AuthController::class,

    /**
     * 登录控制器路由
     */
    'route'              => 'auth',

    /**
     * 对象权限列表
     */
    'object_permissions' => []
];