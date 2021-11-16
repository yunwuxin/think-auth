<?php

namespace yunwuxin\auth\guard;

use think\Request;
use yunwuxin\auth\exception\UnauthorizedHttpException;
use yunwuxin\auth\interfaces\Provider;

class Basic extends Password
{
    protected $request;

    public function __construct(Request $request, Provider $provider)
    {
        parent::__construct($provider);
        $this->request = $request;
    }

    public function authenticate()
    {
        if (!$this->check()) {
            throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
        }
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $credentials = $this->getCredentialsFromRequest();

        if (!empty($credentials)) {
            $user = $this->provider->retrieveByCredentials($credentials);
        }

        return $this->user = $user;
    }

    protected function getCredentialsFromRequest()
    {
        if (!$this->request->server('PHP_AUTH_USER')) {
            return false;
        }

        return [
            'username' => $this->request->server('PHP_AUTH_USER'),
            'password' => $this->request->server('PHP_AUTH_PW'),
        ];
    }
}
