<?php

namespace yunwuxin\auth\credentials;

class TokenCredentials extends BaseCredentials
{
    public function __construct(string $token)
    {
        parent::__construct(['token' => $token]);
    }

    public function getToken()
    {
        return $this->offsetGet('token');
    }
}
