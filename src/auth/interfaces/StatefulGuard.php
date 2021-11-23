<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yunwuxin\auth\interfaces;

/**
 * 保持登录状态
 * Interface StatefulGuard
 * @package yunwuxin\auth\interfaces
 */
interface StatefulGuard extends Guard
{
    public function attempt($credentials, $remember = false);

    /**
     * 设置登录用户
     *
     * @param mixed $user
     * @param bool $remember
     * @return void
     */
    public function login($user, $remember = false);

    /**
     * 用户是否使用了“记住我”
     *
     * @return bool
     */
    public function viaRemember();

    /**
     * 登出
     *
     * @return void
     */
    public function logout();
}
