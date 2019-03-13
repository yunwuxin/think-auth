<?php
//注册路由
use think\facade\Route;

$route = config('auth.route');
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
            'password/reset',
        ], "\\" . $controllers['reset'] . "@showResetForm");
        Route::post("password/reset", "\\" . $controllers['reset'] . "@reset");
    });
}