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

namespace yunwuxin\auth\password;

use Closure;
use think\App;
use UnexpectedValueException;
use yunwuxin\auth\interfaces\CanResetPassword;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\facade\Auth;

class Broker
{

    /** @var Provider */
    protected $provider;

    /**
     * The custom password validator callback.
     *
     * @var \Closure
     */
    protected $passwordValidator;

    /** @var Token */
    protected $token;

    /** @var Auth */
    protected $auth;

    public function __construct(App $app, \yunwuxin\Auth $auth, Token $token)
    {
        $this->token    = $token;
        $this->provider = $auth->createUserProvider($app->config->get('auth.password.provider'));
    }

    /**
     * 发送重置密码链接
     *
     * @param array $credentials
     * @return string
     */
    public function sendResetLink(array $credentials)
    {
        $user = $this->getUser($credentials);
        if (is_null($user)) {
            throw new Exception(Exception::INVALID_USER);
        }
        $user->sendPasswordResetNotification($this->createToken($user));
    }

    /**
     * 重置密码
     *
     * @param array $credentials
     * @param Closure $callback
     * @return string
     */
    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        $pass = $credentials['password'];

        $callback($user, $pass);

        $this->token->delete($user);
    }

    protected function createToken(CanResetPassword $user)
    {
        return $this->token->create($user);
    }

    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            throw new Exception(Exception::INVALID_USER);
        }

        if (!$this->validateNewPassword($credentials)) {
            throw new Exception(Exception::INVALID_PASSWORD);
        }

        if (!$this->token->exists($user, $credentials['token'])) {
            throw new Exception(Exception::INVALID_TOKEN);
        }

        return $user;
    }

    public function validator(Closure $callback)
    {
        $this->passwordValidator = $callback;
    }

    protected function validateNewPassword(array $credentials)
    {
        if (isset($this->passwordValidator)) {
            [$password, $confirm] = [
                $credentials['password'],
                $credentials['password_confirm'],
            ];

            return call_user_func($this->passwordValidator, $credentials) && $password === $confirm;
        }

        return $this->validatePasswordWithDefaults($credentials);
    }

    protected function validatePasswordWithDefaults(array $credentials)
    {
        [$password, $confirm] = [
            $credentials['password'],
            $credentials['password_confirm'],
        ];

        return $password === $confirm && mb_strlen($password) >= 6;
    }

    /**
     * @param array $credentials
     * @return CanResetPassword
     */
    protected function getUser(array $credentials)
    {
        if (isset($credentials['token'])) {
            unset($credentials['token']);
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && !$user instanceof CanResetPassword) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    public function tokenExists(array $credentials, $token)
    {
        if (!is_null($user = $this->getUser($credentials))) {
            return $this->token->exists($user, $token);
        }

        return false;
    }
}
