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
namespace yunwuxin\auth\model;

use think\Model;
use yunwuxin\auth\interfaces\Authorizable;
use yunwuxin\auth\interfaces\CanResetPassword;
use yunwuxin\auth\traits\AuthorizableUser;
use yunwuxin\auth\traits\CanResetPasswordUser;

/**
 * 默认用户模型
 * Class User
 * @package think\auth\model
 */
class User extends Model implements Authorizable, CanResetPassword
{
    use AuthorizableUser, CanResetPasswordUser;
}
