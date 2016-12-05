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
namespace yunwuxin\auth\behavior;

use yunwuxin\auth\exception\AuthenticationException;

/**
 * 用户身份认证
 * Class Authentication
 * @package think\auth\behavior
 */
class Authentication
{
    public function run()
    {
        if (auth()->guest()) {
            throw new AuthenticationException;
        }

    }
}