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
use yunwuxin\auth\Gate;

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
 * @param mixed $user
 * @param string $ability
 * @param array $args
 * @return bool
 */
function can($user, $ability, ...$args)
{
    return Gate::forUser($user)->can($ability, ...$args);
}

Hook::add('app_init', function () {
    //注册路由
    $route = Config::get('auth.route');
    if ($route) {
        $controllers = $route['controllers'];
        Route::group($route['group'], function () use ($controllers) {
            //登录
            Route::get("login", "\\" . $controllers['login'] . "@showLoginForm");
            Route::post("login", "\\" . $controllers['login'] . "@login");
            Route::get("logout", "\\" . $controllers['login'] . "@logout");
            //注册
            Route::get('register', "\\" . $controllers['register'] . "@showRegisterForm");
            Route::post("register", "\\" . $controllers['register'] . "@register");
            //忘记密码
            Route::get('password/forgot', "\\" . $controllers['forgot'] . "@showSendPasswordResetEmailForm");
            Route::post("password/forgot", "\\" . $controllers['forgot'] . "@sendResetLinkEmail");
            //重设密码
            Route::get([
                'AUTH_PASSWORD',
                'password/reset'
            ], "\\" . $controllers['reset'] . "@showResetForm");
            Route::post("password/reset", "\\" . $controllers['reset'] . "@reset");
        });

    }
});