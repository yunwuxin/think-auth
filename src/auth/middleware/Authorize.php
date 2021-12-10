<?php

namespace yunwuxin\auth\middleware;

use Closure;
use yunwuxin\Auth;
use yunwuxin\auth\exception\AuthorizationException;

class Authorize
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $ability, ...$args)
    {
        $user = $this->auth->user();

        if (!can($user, $ability, ...$args)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }
}
