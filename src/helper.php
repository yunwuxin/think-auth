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
use yunwuxin\facade\Gate;

/**
 * @param mixed $user
 * @param string $ability
 * @param array $args
 * @return bool
 */
function can($user, $ability, ...$args)
{
    return Gate::forUser($user)->can($ability, ...$args);
}
