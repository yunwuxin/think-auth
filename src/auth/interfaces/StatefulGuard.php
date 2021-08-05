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

    /**
     * 尝试登录
     *
     * @param array $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true);

    /**
     * 登录（当前请求有效）
     *
     * @param array $credentials
     * @return bool
     */
    public function once(array $credentials = []);

    /**
     * 设置登录用户
     *
     * @param StatefulUser $user
     * @param bool $remember
     * @return void
     */
    public function login(StatefulUser $user, $remember = false);

    /**
     * 通过用户id登录
     *
     * @param mixed $id
     * @param bool $remember
     * @return bool|StatefulUser
     */
    public function loginUsingId($id, $remember = false);

    /**
     * 通过用户id登录（当前请求有效）
     *
     * @param mixed $id
     * @return bool|StatefulUser
     */
    public function onceUsingId($id);

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
