<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think;


use think\auth\Authenticatable;
use think\auth\UserProvider;

class Auth
{
    private static $_instance;

    /** @var  UserProvider */
    protected $provider;

    /**
     * @var bool
     */
    protected $loggedOut = false;

    protected $tokenRetrievalAttempted = false;

    protected $viaRemember = false;

    protected $lastAttempted;

    /**
     * @var Authenticatable
     */
    protected $user;

    protected function __construct(UserProvider $userProvider)
    {
        $this->provider = $userProvider;
    }

    /**
     * @return Auth
     */
    public static function instance()
    {
        if (!(self::$_instance instanceof self)) {
            $model           = Config::get('auth.model') ?: '\\app\\model\\User';
            $userProvider    = new UserProvider($model);
            self::$_instance = new self($userProvider);
        }
        return self::$_instance;
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function id()
    {
        if ($this->loggedOut) {
            return;
        }

        $id = Session::get($this->getName()) ?: $this->getRecallerId();

        if (is_null($id) && $this->user()) {
            $id = $this->user()->getAuthIdentifier();
        }

        return $id;
    }


    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = Session::get($this->getName());

        $user = null;

        if (!is_null($id)) {
            $user = $this->provider->retrieveById($id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && !is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                Session::set($this->getName(), $user->getAuthIdentifier());
            }
        }

        return $this->user = $user;
    }

    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }


    public function attempt($credentials, $remember = false, $login = true)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        return false;
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return $user && $this->provider->validateCredentials($user, $credentials);
    }


    /**
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function login($user, $remember = false)
    {
        Session::set($this->getName(), $user->getAuthIdentifier());

        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);

            $this->createRecaller($user);
        }


        $this->setUser($user);
    }


    public function logout()
    {
        $user = $this->user();

        $this->clearUserDataFromStorage();

        if (!is_null($this->user)) {
            $this->refreshRememberToken($user);
        }

        $this->user = null;

        $this->loggedOut = true;
    }


    protected function clearUserDataFromStorage()
    {
        Session::delete($this->getName());

        if (!is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();
            Cookie::delete($recaller);
        }
    }


    /**
     * @param Authenticatable $user
     * @return mixed
     */
    protected function createRecaller($user)
    {
        $value = $user->getAuthIdentifier() . '|' . $user->getRememberToken();
        return Cookie::set($this->getRecallerName(), $value);
    }


    public function setUser($user)
    {
        $this->user = $user;

        $this->loggedOut = false;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * @param Authenticatable $user
     */
    protected function createRememberTokenIfDoesntExist($user)
    {
        if (empty($user->getRememberToken())) {
            $this->refreshRememberToken($user);
        }
    }

    /**
     * @param Authenticatable $user
     */
    protected function refreshRememberToken($user)
    {
        $user->setRememberToken($token = md5(time() . mt_rand(0, 1000)));

        $this->provider->updateRememberToken($user, $token);
    }


    /**
     * @param $recaller
     * @return Authenticatable
     */
    protected function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && !$this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $token) = explode('|', $recaller, 2);

            $this->viaRemember = !is_null($user = $this->provider->retrieveByToken($id, $token));

            return $user;
        }
    }

    protected function getRecaller()
    {
        return Cookie::get($this->getRecallerName());
    }

    public function getRecallerName()
    {
        return 'remember_' . md5(get_class($this));
    }

    /**
     * Get the user ID from the recaller cookie.
     *
     * @return string|null
     */
    protected function getRecallerId()
    {
        if ($this->validRecaller($recaller = $this->getRecaller())) {
            return reset(explode('|', $recaller));
        }
    }

    /**
     * Determine if the recaller cookie is in a valid format.
     *
     * @param  string $recaller
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if (!is_string($recaller) || strrpos('|', $recaller) === false) {
            return false;
        }

        $segments = explode('|', $recaller);

        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }

    public function getName()
    {
        return 'login_' . md5(get_class($this));
    }

}