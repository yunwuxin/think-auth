<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace yunwuxin\auth\guard;

use think\Cookie;
use think\Event;
use think\helper\Str;
use think\Request;
use yunwuxin\auth\event\Login;
use yunwuxin\auth\exception\UnauthorizedHttpException;
use yunwuxin\auth\interfaces\Authorizable;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\StatefulGuard;
use yunwuxin\auth\interfaces\StatefulProvider;
use yunwuxin\auth\interfaces\StatefulUser;
use yunwuxin\auth\interfaces\SupportsBasicAuth;
use yunwuxin\auth\traits\GuardHelpers;

class Session implements Guard, StatefulGuard, SupportsBasicAuth
{
    use GuardHelpers;

    /** @var StatefulProvider */
    protected $provider;

    /**
     * 上次通过认证的用户
     *
     * @var StatefulUser
     */
    protected $lastAttempted;

    /**
     * 是否通过cookie记住用户
     *
     * @var bool
     */
    protected $viaRemember = false;

    /** @var bool 是否登出 */
    protected $loggedOut = false;

    protected $tokenRetrievalAttempted = false;

    protected $session;

    protected $event;

    protected $cookie;

    protected $request;

    public function __construct(StatefulProvider $provider, \think\Session $session, Event $event, Cookie $cookie, Request $request)
    {
        $this->provider = $provider;
        $this->session  = $session;
        $this->event    = $event;
        $this->cookie   = $cookie;
        $this->request  = $request;
    }

    /**
     * 获取通过认证的用户
     *
     * @return StatefulUser|Authorizable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        $user = null;

        if (!is_null($id)) {
            $user = $this->provider->retrieveById($id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && !is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                $this->session->set($this->getName(), $user->getAuthId());

                $this->event->trigger(new Login($user, true));
            }
        }

        return $this->user = $user;
    }

    /**
     * 认证用户
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * 设置当前用户
     *
     * @param mixed $user
     * @return Session
     */
    public function setUser($user)
    {
        $this->user      = $user;
        $this->loggedOut = false;
        return $this;
    }

    /**
     * 获取上次通过认证的用户
     *
     * @return StatefulUser
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Session键名
     *
     * @return string
     */
    protected function getName()
    {
        return 'login_' . sha1(static::class);
    }

    public function getRecallerName()
    {
        return 'remember_' . sha1(static::class);
    }

    protected function getRecaller()
    {
        return $this->request->cookie($this->getRecallerName());
    }

    protected function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && !$this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $token) = explode('|', $recaller, 2);

            $this->viaRemember = !is_null($user = $this->provider->retrieveByToken($id, $token));

            return $user;
        }
    }

    protected function validRecaller($recaller)
    {
        if (!is_string($recaller) || !Str::contains($recaller, '|')) {
            return false;
        }

        $segments = explode('|', $recaller);

        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }

    /**
     * 尝试登录
     *
     * @param array $credentials
     * @param bool  $remember
     * @param bool  $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
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

    /**
     * 登录（当前请求有效）
     *
     * @param array $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * 设置登录用户
     *
     * @param StatefulUser $user
     * @param bool         $remember
     * @return void
     */
    public function login(StatefulUser $user, $remember = false)
    {
        $this->session->set($this->getName(), $user->getAuthId());

        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);
            $this->createRecaller($user);
        }

        $this->event->trigger(new Login($user, $remember));

        $this->setUser($user);
    }

    /**
     * 通过用户id登录
     *
     * @param mixed $id
     * @param bool  $remember
     * @return false|StatefulUser
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = $this->provider->retrieveById($id);

        if (!is_null($user)) {
            $this->login($user, $remember);

            return $user;
        }

        return false;
    }

    /**
     * 通过用户id登录（当前请求有效）
     *
     * @param mixed $id
     * @return bool|StatefulUser
     */
    public function onceUsingId($id)
    {
        $user = $this->provider->retrieveById($id);

        if (!is_null($user)) {
            $this->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * 用户是否使用了“记住我”
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }

    /**
     * 登出
     *
     * @return void
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

    public function basic($field = 'email', $extraConditions = [])
    {
        if ($this->check()) {
            return;
        }

        if ($this->attemptBasic($field, $extraConditions)) {
            return;
        }

        return $this->failedBasicResponse();
    }

    public function onceBasic($field = 'email', $extraConditions = [])
    {
        $credentials = $this->basicCredentials($field);

        if (!$this->once(array_merge($credentials, $extraConditions))) {
            return $this->failedBasicResponse();
        }
    }

    protected function attemptBasic($field, $extraConditions = [])
    {
        if (!$this->request->server('PHP_AUTH_USER')) {
            return false;
        }

        return $this->attempt(array_merge(
            $this->basicCredentials($field), $extraConditions
        ));
    }

    protected function basicCredentials($field)
    {
        return [$field => $this->request->server('PHP_AUTH_USER'), 'password' => $this->request->server('PHP_AUTH_PW')];
    }

    protected function failedBasicResponse()
    {
        throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }

    protected function clearUserDataFromStorage()
    {
        $this->session->delete($this->getName());

        if (!is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();
            $this->cookie->delete($recaller);
        }
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    protected function createRememberTokenIfDoesntExist(StatefulUser $user)
    {
        if (empty($user->getRememberToken())) {
            $this->refreshRememberToken($user);
        }
    }

    protected function refreshRememberToken(StatefulUser $user)
    {
        $user->setRememberToken($token = Str::random(60));

        $this->provider->updateRememberToken($user, $token);
    }

    protected function createRecaller(StatefulUser $user)
    {
        $value = $user->getAuthId() . '|' . $user->getRememberToken();
        $this->cookie->forever($this->getRecallerName(), $value);
    }

}
