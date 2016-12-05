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

abstract class Guard
{
    /** @var Authenticatable 当前用户 */
    protected $user;

    /** @var Provider */
    protected $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * 是否通过认证
     * @return mixed
     */
    abstract public function check();

    /**
     * 获取通过认证的用户
     *
     * @return Authenticatable|null
     */
    abstract public function user();

    /**
     * 用户id
     *
     * @return int|null
     */
    abstract public function id();

    /**
     * 认证用户
     *
     * @param  array $credentials
     * @return bool
     */
    abstract public function validate(array $credentials = []);

    /**
     * 设置当前用户
     *
     * @param  Authenticatable $user
     * @return void
     */
    abstract public function setUser(Authenticatable $user);
}