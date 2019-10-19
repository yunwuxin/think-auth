<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2019/3/12
 * Time: 17:55
 */

namespace yunwuxin\auth;

use think\Route;

class Service extends \think\Service
{
    public function boot()
    {
        $config = $this->app->config->get('auth.route');
        if ($config) {
            $this->registerRoutes(function (Route $route) use ($config) {

                $controllers = $config['controllers'];

                $route->group($config['group'], function () use ($route, $controllers) {
                    //登录
                    $route->get("login", $controllers['login'] . "@showLoginForm");
                    $route->post("login", $controllers['login'] . "@login");
                    $route->get("logout", $controllers['login'] . "@logout");
                    //注册
                    $route->get('register', $controllers['register'] . "@showRegisterForm");
                    $route->post("register", $controllers['register'] . "@register");
                    //忘记密码
                    $route->get('password/forgot', $controllers['forgot'] . "@showSendPasswordResetEmailForm");
                    $route->post("password/forgot", $controllers['forgot'] . "@sendResetLinkEmail");
                    //重设密码
                    $route->get('password/reset', $controllers['reset'] . "@showResetForm")->name('AUTH_PASSWORD');
                    $route->post("password/reset", $controllers['reset'] . "@reset");
                });
            });
        }
    }
}
