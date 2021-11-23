<?php

namespace yunwuxin\auth\guard;

use think\helper\Str;
use think\Request;
use yunwuxin\auth\credentials\PasswordCredential;
use yunwuxin\auth\exception\UnauthorizedHttpException;
use yunwuxin\auth\interfaces\Provider;

class Basic extends Password
{
    protected $request;

    public function __construct(Provider $provider, Request $request)
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

    protected function retrieveUser()
    {
        $credentials = $this->getCredentialsFromRequest();

        if (!empty($credentials)) {
            return $this->provider->retrieveByCredentials($credentials);
        }

        return null;
    }

    protected function getCredentialsFromRequest()
    {
        $header = $this->request->header('Authorization');

        if (!empty($header)) {
            if (Str::startsWith($header, 'Basic ')) {
                $token   = Str::substr($header, 6);
                $decoded = base64_decode($token);
                [$username, $password] = explode(':', $decoded);

                return new PasswordCredential($username, $password);
            }
        }

        return false;
    }
}
