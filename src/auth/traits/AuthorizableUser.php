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

namespace yunwuxin\auth\traits;

use yunwuxin\auth\Gate;
use yunwuxin\auth\Role;

trait AuthorizableUser
{
    /**
     * 获取用户角色
     * @return Role[]
     */
    public function getRoles()
    {
        throw new \LogicException('You must override the getRole() method in the concrete user class.');
    }

    /**
     * 是否具有某个角色
     * @param array|string $name
     * @param bool         $requireAll
     * @return bool
     */
    public function hasRole($name, $requireAll = false)
    {
        return Gate::forUser($this)->hasRole($name, $requireAll);
    }

    /**
     * 获取用户的所有权限
     * @return array
     */
    public function getPermissions()
    {
        return Gate::forUser($this)->getPermissions();
    }

    /**
     * 是否具有某个权限
     * @param      $name
     * @param bool $requireAll
     * @return bool
     */
    public function hasPermission($name, $requireAll = false)
    {
        return Gate::forUser($this)->hasPermission($name, $requireAll);
    }

    /**
     * 检查权限
     * @param       $ability
     * @param array $args
     * @return bool|mixed
     */
    public function can($ability, ...$args)
    {
        return Gate::forUser($this)->can($ability, ...$args);
    }
}