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
use yunwuxin\Auth;
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
    return Auth::instance();
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
    $route = Config::get('auth.route');
    if ($route) {
        Route::group($route, function () {
            //登录
            Route::get("login", "\\yunwuxin\\auth\\controller\\LoginController@showLoginForm");
            Route::post("login", "\\yunwuxin\\auth\\controller\\LoginController@login");
            Route::get("logout", "\\yunwuxin\\auth\\controller\\LoginController@logout");
            //注册
            Route::get('register', "\\yunwuxin\\auth\\controller\\RegisterController@showRegisterForm");
            Route::post("register", "\\yunwuxin\\auth\\controller\\LoginController@register");
            //忘记密码
            Route::get('password/forget', "\\yunwuxin\\auth\\controller\\ForgotPasswordController@showSendPasswordResetEmailForm");
            Route::post("password/forget", "\\yunwuxin\\auth\\controller\\ForgotPasswordController@sendResetLinkEmail");
            //重设密码
            Route::get([
                'AUTH_PASSWORD',
                'password/reset'
            ], "\\yunwuxin\\auth\\controller\\ResetPasswordController@showResetForm");
            Route::post("password/reset", "\\yunwuxin\\auth\\controller\\ResetPasswordController@reset");

        });

    }
});