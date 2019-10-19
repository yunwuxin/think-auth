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

namespace yunwuxin\auth\interfaces;

interface StatefulUser
{

    /**
     * 获取用户ID
     * @return mixed
     */
    public function getAuthId();

    /**
     * 获取密码
     * @return mixed
     */
    public function getAuthPassword();

    /**
     * 获取“记住我”令牌
     * @return mixed
     */
    public function getRememberToken();

    /**
     * 设置“记住我”令牌
     * @param $token
     * @return mixed
     */
    public function setRememberToken($token);

    /**
     * 获取“记住我”令牌字段名
     * @return mixed
     */
    public function getRememberTokenName();
}
