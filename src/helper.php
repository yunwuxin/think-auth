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
use think\Auth;
use think\Config;
use think\helper\Hash;
use think\Hook;
use think\Route;

/**
 * 加密
 * @param $value
 * @return bool|string
 */
function encrypt($value)
{
    return Hash::make($value);
}

/**
 * @return Auth
 */
function auth()
{
    return Auth::make();
}

/**
 * @param      $permission
 * @param null $object
 * @return bool
 */
function can($permission, $object = null)
{
    return true;
}

/**
 * @param $role
 * @return bool
 */
function has_role($role)
{
    return true;
}

Hook::add('app_init', function () {
    //注册路由
    Route::controller(Config::get('auth.route'), Config::get('auth.controller'));
});