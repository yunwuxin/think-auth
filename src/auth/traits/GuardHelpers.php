<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/5/11
 * Time: 18:21
 */

namespace yunwuxin\auth\traits;

use yunwuxin\auth\exception\AuthenticationException;

trait GuardHelpers
{
    /** @var mixed 当前用户 */
    protected $user;

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
}
