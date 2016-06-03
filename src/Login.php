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

namespace traits\controller;


use think\Request;

trait Login
{

    protected function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'username';
    }

    protected function getCredentials(Request $request)
    {
        return [
            $this->loginUsername() => $request->param($this->loginUsername()),
            'password'             => $request->param('password')
        ];
    }


    protected function handleUserWasAuthenticated()
    {

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated(auth()->user());
        }

        return redirect($this->redirectPath())->restore();
    }


    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return '/';
    }

    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }

    public function getLogout()
    {
        auth()->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}