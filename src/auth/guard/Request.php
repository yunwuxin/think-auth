<?php

namespace yunwuxin\auth\guard;

use think\helper\Arr;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\auth\traits\GuardHelpers;

class Request implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $callback;

    public function __construct(Provider $provider, \think\Request $request, callable $callback)
    {
        $this->provider = $provider;
        $this->request  = $request;
        $this->callback = $callback;
    }

    static public function __make(Provider $provider, \think\Request $request, $config)
    {
        $callback = Arr::get($config, 'callback');

        return new static($provider, $request, $callback);
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func($this->callback, $this->request);
    }

}
