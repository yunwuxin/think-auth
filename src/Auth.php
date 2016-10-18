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

use think\auth\interfaces\Authenticatable;

class Auth
{
    private static $_instance;

    /** @var  Authenticatable */
    protected $provider;

    /**
     * @var bool
     */
    protected $loggedOut = false;

    protected $tokenRetrievalAttempted = false;

    protected $viaRemember = false;

    /**
     * @var Authenticatable
     */
    protected $user;

    protected function __construct()
    {
        $provider = Config::get('auth.provider');

        if (!is_subclass_of($provider, Authenticatable::class)) {
            throw new \InvalidArgumentException('the provider must instance of ' . Authenticatable::class);
        }
        $this->provider = $provider;
    }

    /**
     * 获取实例
     * @return Auth
     */
    public static function make()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 检查用户身份认证
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * 是否为游客
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * 获取用户id
     * @return mixed|null|string
     */
    public function id()
    {
        if ($this->loggedOut) {
            return null;
        }

        $id = Session::get($this->getSessionKey()) ?: $this->getRecallerId();

        if (is_null($id) && $this->user()) {
            $id = $this->user()->getId();
        }

        return $id;
    }

    /**
     * 获取当前通过认证的用户
     * @return mixed|null|Authenticatable
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = Session::get($this->getSessionKey());

        $user = null;

        if (!is_null($id)) {
            $user = call_user_func([$this->provider, 'retrieveById'], $id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && !is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                Session::set($this->getSessionKey(), $user->getId());
            }
        }

        return $this->user = $user;
    }

    /**
     * 尝试认证用户
     * @param      $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool
     */
    public function attempt($credentials, $remember = false, $login = true)
    {
        $user = call_user_func([$this->provider, 'retrieveByCredentials'], $credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        return false;
    }

    /**
     * 认证用户
     * @param $user
     * @param $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return $user && call_user_func([$this->provider, 'validateCredentials'], $user, $credentials);
    }

    /**
     * 保存登录信息
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function login(Authenticatable $user, $remember = false)
    {
        Session::set($this->getSessionKey(), $user->getId());

        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);

            $this->createRecaller($user);
        }

        $this->setUser($user);
    }

    /**
     * 注销用户信息
     */
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

    /**
     * 清除用户信息
     */
    protected function clearUserDataFromStorage()
    {
        Session::delete($this->getSessionKey());

        if (!is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();
            Cookie::delete($recaller);
        }
    }

    /**
     * 创建记住用户信息
     * @param Authenticatable $user
     * @return mixed
     */
    protected function createRecaller($user)
    {
        $value = $user->getId() . '|' . $user->getRememberToken();
        return Cookie::set($this->getRecallerName(), $value);
    }

    /**
     * 设置当前认证的用户
     * @param Authenticatable $user
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;

        $this->loggedOut = false;
    }

    /**
     * 获取当前认证的用户
     * @return Authenticatable
     */
    public function getUser()
    {
        return $this->user;
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
        $user->setRememberToken(md5(time() . mt_rand(0, 1000)));
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

            $this->viaRemember = !is_null($user = call_user_func([$this->provider, 'retrieveByToken'], $id, $token));

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

    /**
     * Session键名
     * @return string
     */
    protected function getSessionKey()
    {
        return 'login_' . md5(get_class($this));
    }

}