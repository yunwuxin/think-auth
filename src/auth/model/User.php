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
namespace think\auth\model;

use think\auth\interfaces\Authenticatable;
use think\auth\traits\UserModel;
use think\Model;

/**
 * 默认用户模型
 * Class User
 * @package think\auth\model
 */
class User extends Model implements Authenticatable
{
    use UserModel;
}