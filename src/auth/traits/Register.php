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

namespace yunwuxin\auth\traits;

use think\exception\ValidateException;
use think\Request;
use think\Response;
use think\response\View;
use think\Validate;
use yunwuxin\facade\Auth;

trait Register
{

    /**
     * 注册页面
     *
     * @return View
     */
    public function showRegisterForm()
    {
        if ($this->guard()->user()) {
            return redirect($this->redirectPath());
        }

        return view('auth/register');
    }

    public function register(Request $request)
    {
        $this->validate($request);
        $user = $this->create($request);
        $this->guard()->login($user);
        return $this->registered($user)
            ?: redirect($this->redirectPath());
    }

    /**
     * 注册成功后的跳转地址
     *
     * @return string
     */
    protected function redirectPath()
    {
        return '/';
    }

    /**
     * @param mixed $user
     * @return Response|null
     */
    protected function registered($user)
    {
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function create(Request $request)
    {

    }

    /**
     * 生成验证器
     *
     * @param Request $request
     * @return Validate
     */
    protected function validator(Request $request)
    {
        return (new Validate())->batch(true);
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

    protected function guard()
    {
        return Auth::guard();
    }
}
