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

    public function validate(array $credentials = [])
    {
        $this->lastValidated = $this->provider->retrieveByCredentials($credentials);
        return !is_null($this->lastValidated);
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }
    }

    /**
     * 登录（当前请求有效）
     *
     * @param array $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastValidated);

            return true;
        }

        return false;
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
