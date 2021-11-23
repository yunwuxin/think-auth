<?php

namespace yunwuxin\auth\middleware;

use Closure;
use yunwuxin\Auth;

class UseGuard
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard)
    {
        $this->auth->shouldUse($guard);

        return $next($request);
    }
}
