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

namespace yunwuxin\auth;

use yunwuxin\auth\interfaces\Authenticatable;

/**
 * Class Provider
 * @package yunwuxin\auth
 */
abstract class Provider
{
    /** @var Provider */
    protected static $instance;

    /**
     * 根据用户ID取得用户
     * @param $id
     * @return mixed
     */
    abstract public function retrieveById($id);

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return mixed
     */
    abstract public function retrieveByToken($id, $token);

    /**
     * 更新“记住我”的token
     * @param Authenticatable $user
     * @param                 $token
     * @return mixed
     */
    abstract public function updateRememberToken(Authenticatable $user, $token);

    /**
     * 根据用户输入的数据获取用户
     * @param array $credentials
     * @return mixed
     */
    abstract public function retrieveByCredentials(array $credentials);

    /**
     * 验证密码
     * @param       $user
     * @param array $credentials
     * @return mixed
     */
    abstract public function validateCredentials(Authenticatable $user, array $credentials);
}
