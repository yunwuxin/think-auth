<?php

namespace yunwuxin\auth\middleware;

use Closure;
use yunwuxin\Auth;

class AuthenticationWithBasicAuth
{

    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard = null)
    {
        return $this->auth->guard($guard)->basic() ?: $next($request);
    }
}