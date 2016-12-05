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

namespace yunwuxin\auth\traits;

trait AuthenticatableUser
{

    public function getAuthId()
    {
        return $this->data[$this->getPk()];
    }

    public function getAuthPassword()
    {
        return $this->data['password'];
    }

    public function getRememberToken()
    {
        return $this->data[$this->getRememberTokenName()];
    }

    public function setRememberToken($token)
    {
        $this->{$this->getRememberTokenName()} = $token;

        $this->save();
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

}