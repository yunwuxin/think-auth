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

namespace think\auth\traits;

use think\exception\ValidateException;
use think\Request;
use think\Validate;

trait Login
{
    /**
     * 登录时的验证规则，可以重写覆盖
     * @var array
     */
    protected $validateRules = [
        'username|用户名' => 'require',
        'password|密码'  => 'require'
    ];

    /**
     * 是否批量验证
     * @var bool
     */
    protected $validateBatch = true;

    /**
     * 获取用户名字段名
     * @return string
     */
    protected function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'username';
    }

    /**
     * 获取密码字段名
     * @return string
     */
    protected function loginPassword()
    {
        return property_exists($this, 'password') ? $this->password : 'password';
    }

    /**
     * 认证信息
     * @param Request $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return [
            $this->loginUsername() => $request->param($this->loginUsername()),
            $this->loginPassword() => $request->param($this->loginPassword())
        ];
    }

    /**
     * 登录成功后的回调，可代替默认的跳转
     */
    protected function handleUserWasAuthenticated()
    {

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated(auth()->user());
        }

        return redirect($this->redirectPath())->restore();
    }

    /**
     * 登录成功后的跳转地址
     * @return string
     */
    protected function redirectPath()
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return '/';
    }

    /**
     * 获取登录地址
     * @return string
     */
    protected function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
    }

    /**
     * 登录
     * @param Request $request
     */
    public function postLogin(Request $request)
    {
        if (!empty($this->validateRules)) {
            $validate = Validate::make($this->validateRules);

            $validate->batch($this->validateBatch);

            if (!$validate->check($request->param())) {
                throw new ValidateException($validate->getError());
            }
        }

        $credentials = $this->getCredentials($request);

        if (auth()->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated();
        }

        throw new ValidateException([$this->loginUsername() => '用户名或密码错误']);
    }

    /**
     * 登出
     * @return \think\response\Redirect
     */
    public function getLogout()
    {
        auth()->logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}