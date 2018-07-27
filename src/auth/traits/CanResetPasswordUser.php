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

use yunwuxin\auth\notification\ResetPassword;
use yunwuxin\notification\Notifiable;

/**
 * Class CanResetPasswordUser
 * @package yunwuxin\auth\traits
 * @mixin Notifiable
 */
trait CanResetPasswordUser
{

    /**
     * 获取邮箱或者手机号码
     * @return mixed
     */
    public function getEmailForResetPassword()
    {
        return $this->getAttr('email');
    }

    /**
     * 发送重置密码token通知
     * @param $token
     * @return mixed
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($this->getAttr('email'), $token));
    }
}