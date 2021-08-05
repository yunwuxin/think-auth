<?php

namespace yunwuxin\auth\guard;

use think\helper\Arr;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\traits\GuardHelpers;

class Request implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $callback;

    public function __construct(\think\Request $request, callable $callback)
    {
        $this->request  = $request;
        $this->callback = $callback;
    }

    static public function __make(\think\Request $request, $config)
    {
        $callback = Arr::get($config, 'callback');

        return new static($request, $callback);
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func($this->callback, $this->request);
    }

    public function validate(array $credentials = [])
    {
        return !is_null((new static($credentials['request'], $this->callback))->user());
    }
}
