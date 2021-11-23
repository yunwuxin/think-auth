<?php

namespace yunwuxin\auth\credentials;

class PasswordCredential extends BaseCredentials
{
    public function __construct($username, $password)
    {
        parent::__construct([
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function getUsername()
    {
        return $this->offsetGet('username');
    }

    public function getPassword()
    {
        return $this->offsetGet('password');
    }
}
