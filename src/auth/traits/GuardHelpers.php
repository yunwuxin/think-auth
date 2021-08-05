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

    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException;
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
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
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
