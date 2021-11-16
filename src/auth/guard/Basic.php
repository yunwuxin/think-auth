<?php

namespace yunwuxin\auth\guard;

use think\helper\Str;
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
        $header = $this->request->header('Authorization');

        if (!empty($header)) {
            if (Str::startsWith($header, 'Basic ')) {
                $token   = Str::substr($header, 6);
                $decoded = base64_decode($token);
                [$username, $password] = explode(':', $decoded);
                return [
                    'username' => $username,
                    'password' => $password,
                ];
            }
        }

        return false;
    }
}
