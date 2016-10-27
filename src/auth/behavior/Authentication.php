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
namespace think\auth\behavior;

use think\auth\exception\AuthenticationException;
use think\auth\interfaces\Authenticatable;
use think\auth\interfaces\Authorizable;

/**
 * 用户身份认证
 * Class Authentication
 * @package think\auth\behavior
 */
class Authentication
{
    public function run()
    {
        /** @var Authenticatable|Authorizable $user */
        $user = auth()->user();

        $routeInfo = request()->routeInfo();

        if (isset($routeInfo['option']['roles'])) {
            if (!$user->hasRole($routeInfo['option']['roles'])) {
                throw new AuthenticationException;
            }
        }

        if (isset($routeInfo['option']['permissions'])) {
            return true;
        }

    }
}