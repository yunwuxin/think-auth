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

namespace yunwuxin\auth\traits;

use think\exception\ValidateException;
use think\Request;
use think\Response;
use think\Validate;
use yunwuxin\facade\Auth;

trait Login
{
    /**
     * 登录
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request);

        if ($this->attempt($request)) {
            return $this->logined($this->guard()->user()) ?: redirect($this->redirectPath())->restore();
        }

        throw new ValidateException([$this->username() => '用户名或密码错误']);
    }

    /**
     * 登录页面
     *
     * @return \think\response\Redirect|\think\response\View
     */
    public function showLoginForm()
    {
        if ($this->guard()->user()) {
            return redirect($this->redirectPath());
        }
        return view('auth/login');
    }

    /**
     * 登出
     *
     * @return \think\response\Redirect
     */
    public function logout()
    {
        $this->guard()->logout();

        return redirect('/');
    }

    /**
     * 获取用户名字段名
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * 获取密码字段名
     *
     * @return string
     */
    protected function password()
    {
        return 'password';
    }

    /**
     * 认证信息
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            $this->username() => $request->param($this->username()),
            $this->password() => $request->param($this->password()),
        ];
    }

    /**
     * @param $user
     * @return Response
     */
    protected function logined($user)
    {
        //
    }

    /**
     * 登录成功后的跳转地址
     *
     * @return string
     */
    protected function redirectPath()
    {
        return '/';
    }

    /**
     * 生成验证器
     *
     * @param Request $request
     * @return Validate
     */
    protected function validator(Request $request)
    {
        return Validate::make([
            $this->username() => 'require',
            $this->password() => 'require',
        ], [], [
            $this->username() => '用户名',
            $this->password() => '密码',
        ])->batch(true);
    }

    /**
     * 验证
     *
     * @param Request $request
     */
    protected function validate(Request $request)
    {
        $validator = $this->validator($request);

        if (!$validator->check($request->param())) {
            throw new ValidateException($validator->getError());
        }
    }

    /**
     * 登录验证
     *
     * @param Request $request
     * @return bool
     */
    protected function attempt(Request $request)
    {
        return $this->guard()->attempt($this->credentials($request), $request->has('remember'));
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
