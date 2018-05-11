<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/5/11
 * Time: 18:21
 */

namespace yunwuxin\auth\traits;


use yunwuxin\auth\exception\AuthenticationException;
use yunwuxin\auth\interfaces\Authenticatable;
use yunwuxin\auth\Provider;

trait GuardHelpers
{
    /** @var Authenticatable 当前用户 */
    protected $user;

    /** @var Provider */
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
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthId();
        }
    }

    /**
     * Set the current user.
     *
     * @param  Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        return $this;
    }
}