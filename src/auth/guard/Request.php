<?php

namespace yunwuxin\auth\guard;

use yunwuxin\auth\credentials\RequestCredentials;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\auth\traits\GuardHelpers;

class Request implements Guard
{
    use GuardHelpers;

    protected $request;

    public function __construct(Provider $provider, \think\Request $request)
    {
        $this->provider = $provider;
        $this->request  = $request;
    }

    protected function retrieveUser()
    {
        $credentials = new RequestCredentials($this->request);
        return $this->provider->retrieveByCredentials($credentials);
    }

}
