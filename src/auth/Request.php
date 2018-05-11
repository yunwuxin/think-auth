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

namespace yunwuxin\auth;

use yunwuxin\auth\traits\AuthUser;

class Request extends \think\Request
{
    use AuthUser;

    public function getUser()
    {
        return $this->server('PHP_AUTH_USER');
    }

    public function getPassword()
    {
        return $this->server('PHP_AUTH_PW');
    }
}