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

namespace yunwuxin\auth\middleware;

use Closure;
use think\Request;
use yunwuxin\Auth;
use yunwuxin\auth\exception\AuthorizationException;
use yunwuxin\auth\interfaces\Authorizable;

/**
 * 权限管理
 * Class Authorization
 *
 * @package think\auth\behavior
 */
class Authorization
{

    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /** @var Authorizable $user */
        $user = $this->auth->user();

        $rule = $request->rule();

        if ($rule) {
            $roles = $rule->getOption('roles', []);
            if (!empty($roles) && !$user->hasRole($roles)) {
                throw new AuthorizationException();
            }

            $permissions = $rule->getOption('permissions', []);
            $rest        = $rule->getOption('rest');

            if (!empty($permissions) && $rest && $this->isAssoc($permissions)) {
                if (isset($permissions['*']) && !$user->hasPermission($permissions['*'], true)) {
                    throw new AuthorizationException;
                }
                if (isset($permissions[$rest]) && !$user->hasPermission($permissions[$rest], true)) {
                    throw new AuthorizationException;
                }
            } elseif (!$user->hasPermission($permissions, true)) {
                throw new AuthorizationException;
            }
        }

        return $next($request);
    }

    /**
     * 是否为关联数组
     *
     * @param array $arr
     * @return bool
     */
    private function isAssoc($arr)
    {
        return is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }
}
