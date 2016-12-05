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
namespace yunwuxin\auth\guard;

use yunwuxin\auth\Guard;
use yunwuxin\auth\interfaces\Authenticatable;

class Token extends Guard
{

    /**
     * 是否通过认证
     * @return mixed
     */
    public function check()
    {
        // TODO: Implement check() method.
    }

    /**
     * 获取通过认证的用户
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        // TODO: Implement user() method.
    }

    /**
     * 用户id
     *
     * @return int|null
     */
    public function id()
    {
        // TODO: Implement id() method.
    }

    /**
     * 认证用户
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    /**
     * 设置当前用户
     *
     * @param  Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        // TODO: Implement setUser() method.
    }
}