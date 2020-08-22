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
use yunwuxin\Auth;
use yunwuxin\auth\exception\AuthenticationException;

/**
 * 用户身份认证
 * Class Authentication
 *
 * @package think\auth\behavior
 */
class Authentication
{

    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guards = null)
    {
        $this->authenticate((array) $guards);

        return $next($request);
    }

    protected function authenticate($guards)
    {
        if (empty($guards)) {
            return $this->auth->authenticate();
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard)->user();
            }
        }

        throw new AuthenticationException();
    }
}
