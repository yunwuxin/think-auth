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

namespace think\auth\interfaces;

use think\auth\Role;

interface Authorizable
{
    /**
     * 获取用户角色
     * @return Role
     */
    public function getRole();


    /**
     * 是否为超级管理员[拥有所有的权限]
     * @return boolean
     */
    public function isSuperAdmin();
}