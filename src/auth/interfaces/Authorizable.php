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

namespace yunwuxin\auth\interfaces;

use yunwuxin\auth\Role;

interface Authorizable
{
    /**
     * 获取用户角色
     * @return Role[]
     */
    public function getRoles();

    /**
     * 是否具有某个角色
     * @param array|string $name
     * @param bool         $requireAll
     * @return bool
     */
    public function hasRole($name, $requireAll = false);

    /**
     * 获取用户的所有权限
     * @return array
     */
    public function getPermissions();

    /**
     * 是否具有某个权限
     * @param      $name
     * @param bool $requireAll
     * @return bool
     */
    public function hasPermission($name, $requireAll = false);

}