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

use think\helper\Str;
use think\Request;
use yunwuxin\auth\Guard;
use yunwuxin\auth\interfaces\Authenticatable;

class Token extends Guard
{

    /**
     * 是否通过认证
     * @return mixed
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * 获取通过认证的用户
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenFromRequest();

        if (!empty($token)) {
            $user = $this->provider->retrieveByCredentials(
                ['token' => $token]
            );
        }

        return $this->user = $user;
    }

    protected function getTokenFromRequest()
    {
        $request = Request::instance();
        $token   = $request->param('access-token');
        if (empty($token)) {
            $header = $request->header('Authorization');
            if (Str::startsWith($header, 'Bearer ')) {
                $token = Str::substr($header, 7);
            }
        }

        return $token;
    }

    /**
     * 用户id
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
     * 认证用户
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['token'])) {
            return false;
        }

        $credentials = ['token' => $credentials['token']];

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * 设置当前用户
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