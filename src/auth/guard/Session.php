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
use yunwuxin\auth\credentials\BaseCredentials;
use yunwuxin\auth\credentials\PasswordCredential;
use yunwuxin\auth\event\Login;
use yunwuxin\auth\interfaces\StatefulGuard;
use yunwuxin\auth\interfaces\StatefulProvider;

class Session extends Password implements StatefulGuard
{

    /**
     * 是否通过cookie记住用户
     *
     * @var bool
     */
    protected $viaRemember = false;

    protected $tokenRetrievalAttempted = false;

    protected $session;

    protected $event;

    protected $cookie;

    protected $request;

    public function __construct(StatefulProvider $provider, \think\Session $session, Event $event, Cookie $cookie, Request $request)
    {
        $this->session = $session;
        $this->event   = $event;
        $this->cookie  = $cookie;
        $this->request = $request;
        parent::__construct($provider);
    }

    protected function retrieveUser()
    {
        $id = $this->session->get($this->getName());

        $user = null;

        if (!is_null($id)) {
            $user = $this->provider->retrieveById($id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && !is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                $this->session->set($this->getName(), $this->provider->getId($user));

                $this->event->trigger(new Login($user, true));
            }
        }

        return $user;
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

            [$id, $token] = explode('|', $recaller, 2);

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
     * @param bool $remember
     * @return bool
     */
    public function attempt($credentials, $remember = false)
    {
        if (!$credentials instanceof BaseCredentials) {
            $credentials = PasswordCredential::fromArray($credentials);
        }

        if ($this->validate($credentials)) {
            $this->login($this->lastValidated, $remember);
            return true;
        }

        return false;
    }

    /**
     * 设置登录用户
     *
     * @param mixed $user
     * @param bool $remember
     * @return void
     */
    public function login($user, $remember = false)
    {
        $this->session->set($this->getName(), $this->provider->getId($user));

        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);
            $this->createRecaller($user);
        }

        $this->event->trigger(new Login($user, $remember));

        $this->setUser($user);
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
    }

    protected function clearUserDataFromStorage()
    {
        $this->session->delete($this->getName());

        if (!is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();
            $this->cookie->delete($recaller);
        }
    }

    protected function createRememberTokenIfDoesntExist($user)
    {
        if (empty($this->provider->getRememberToken($user))) {
            $this->refreshRememberToken($user);
        }
    }

    protected function refreshRememberToken($user)
    {
        $this->provider->setRememberToken($user, Str::random(60));
    }

    protected function createRecaller($user)
    {
        $value = $this->provider->getId($user) . '|' . $this->provider->getRememberToken($user);
        $this->cookie->forever($this->getRecallerName(), $value);
    }

}
