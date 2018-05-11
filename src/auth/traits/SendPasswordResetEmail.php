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
use think\Validate;
use yunwuxin\auth\password\Broker;
use yunwuxin\auth\password\Exception;

trait SendPasswordResetEmail
{
    public function showSendPasswordResetEmailForm()
    {
        return view('auth/password/email');
    }

    public function sendResetLinkEmail(Request $request, Broker $broker)
    {
        $this->validate($request);

        try {
            $broker->sendResetLink($request->only('email'));
        } catch (Exception $e) {
            throw new ValidateException(['email' => $this->getExceptionMessage($e->getMessage())]);
        }

        return $this->sended()
            ?: redirect($this->redirectPath());

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

    /**
     * @return Response
     */
    protected function sended()
    {

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
        return Validate::make([
            'email' => 'require|email'
        ])->batch(true);
    }
}