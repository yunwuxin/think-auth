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
namespace yunwuxin\auth\notification;

use think\queue\ShouldQueue;
use yunwuxin\Notification;
use yunwuxin\notification\message\Mail;
use yunwuxin\notification\Notifiable;

class ResetPassword extends Notification implements ShouldQueue
{

    public $token;

    public $email;

    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * 发送渠道
     * @param Notifiable $notifiable
     * @return array
     */
    public function channels($notifiable)
    {
        return ['mail'];
    }

    /**
     * @param $notifiable
     * @return Mail
     */
    public function toMail($notifiable)
    {
        return (new Mail())
            ->subject("找回密码")
            ->line("您收到此电子邮件，是因为我们收到了您的帐户的密码重置请求。")
            ->action('重置密码', url('AUTH_PASSWORD', ['email' => $this->email, 'token' => $this->token], true, true))
            ->to($this->email)
            ->line("如果您没有请求密码重置，则忽略此邮件。");
    }
}