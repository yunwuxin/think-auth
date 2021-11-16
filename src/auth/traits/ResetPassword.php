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
use think\helper\Str;
use think\Request;
use think\Response;
use think\Validate;
use yunwuxin\auth\password\Broker;
use yunwuxin\auth\password\Exception;
use yunwuxin\facade\Auth;

trait ResetPassword
{
    public function showResetForm(Request $request, $token, $email)
    {
        return view('auth/password/reset', [
            ['token' => $token, 'email' => $email],
        ]);
    }

    public function reset(Request $request, Broker $broker)
    {
        $this->validate($request);

        try {
            $broker->reset(
                $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            });
        } catch (Exception $e) {
            throw new ValidateException(['email' => $this->getExceptionMessage($e->getMessage())]);
        }

        return $this->reseted()
            ?: redirect($this->redirectPath());
    }

    protected function validate(Request $request)
    {
        $validator = $this->validator($request);

        if (!$validator->check($request->param())) {
            throw new ValidateException($validator->getError());
        }
    }

    /**
     * 生成验证器
     *
     * @param Request $request
     * @return Validate
     */
    protected function validator(Request $request)
    {
        return (new Validate)->rule([
            'token'    => 'require',
            'email'    => 'require|email',
            'password' => 'require|confirm:password_confirm|min:6',
        ])->batch(true);
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            ['email', 'password', 'password_confirm', 'token']
        );
    }

    /**
     * @param mixed $user
     * @param string $password
     */
    protected function resetPassword($user, $password)
    {
        $user->save([
            'password'       => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => Str::random(60),
        ]);

        $this->guard()->login($user);
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function getExceptionMessage($message)
    {
        switch ($message) {
            case Exception::INVALID_USER:
                return '用户不存在';
            case Exception::INVALID_TOKEN:
                return '令牌错误或已过期';
            case Exception::INVALID_PASSWORD:
                return '两次输入的密码不一样';
        }
    }

    /**
     * @return Response
     */
    protected function reseted()
    {

    }

    /**
     * 发送后的跳转地址
     *
     * @return string
     */
    protected function redirectPath()
    {
        return '/';
    }
}
