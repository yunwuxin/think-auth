<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yunwuxin\auth\traits;

use yunwuxin\auth\exception\AuthorizationException;
use yunwuxin\auth\Request;

/**
 * 控制器鉴权
 * Class Authorize
 * @package yunwuxin\auth\traits
 */
class Authorize
{
    public function __call($method, $args)
    {
        if (preg_match('/^authorize_(\w+)(?:\|(\w+))?$/', $method, $match)) {
            $user   = Request::instance()->user();
            $action = $match[1];
            $object = $match[2];

            if (!$user || !$user->can($action, $object)) {
                throw new AuthorizationException;
            }
        } else {
            throw new \ErrorException("Call to undefined method {$method}");
        }
    }
}