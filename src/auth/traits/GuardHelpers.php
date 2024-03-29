<?php

namespace yunwuxin\auth\traits;

use yunwuxin\auth\exception\AuthenticationException;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\auth\interfaces\StatefulProvider;

trait GuardHelpers
{
    /** @var mixed 当前用户 */
    protected $user;

    /** @var Provider|StatefulProvider */
    protected $provider;

    protected $lastValidated;

    public function validate($credentials)
    {
        $this->lastValidated = $this->provider->retrieveByCredentials($credentials);
        return !is_null($this->lastValidated);
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }
        return $this->user = $this->retrieveUser();
    }

    protected function retrieveUser()
    {
        return null;
    }

    public function authenticate()
    {
        if (!$this->check()) {
            throw new AuthenticationException;
        }
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Set the current user.
     *
     * @param  $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
