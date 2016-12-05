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

use yunwuxin\auth\Role;
use think\Config;

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
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);
                if ($hasRole && !$requireAll) {
                    return true;
                } elseif (!$hasRole && $requireAll) {
                    return false;
                }
            }
            return $requireAll;
        } else {
            foreach ($this->getRoles() as $role) {
                if ($role->getName() == $name) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * 获取用户的所有权限
     * @return array
     */
    public function getPermissions()
    {
        $roles       = $this->getRoles();
        $permissions = [];
        array_map(function (Role $role) use (&$permissions) {
            $permissions = array_merge($permissions, $role->getPermissions());
        }, $roles);
        return $permissions;
    }

    /**
     * 是否具有某个权限
     * @param      $name
     * @param bool $requireAll
     * @return bool
     */
    public function hasPermission($name, $requireAll = false)
    {
        if (is_array($name)) {
            foreach ($name as $permissionName) {
                $hasPermission = $this->hasPermission($permissionName);
                if ($hasPermission && !$requireAll) {
                    return true;
                } elseif (!$hasPermission && $requireAll) {
                    return false;
                }
            }
            return $requireAll;
        } else {
            foreach ($this->getPermissions() as $permission) {
                //TODO 正则匹配
                if ($permission == $name) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * 检查权限
     * @param      $action
     * @param null $object
     * @return bool|mixed
     */
    public function can($action, $object = null)
    {
        $object_permissions          = Config::get('auth.object_permissions', []);
        $object_permission_namespace = Config::get('auth.object_permission_namespace');
        if (!is_null($object)) {
            if (is_string($object)) {
                //直接传入类名的情况
                $object_class = $object;
                $object       = null;
            } else {
                $object_class = get_class($object);
            }
            if (array_key_exists($object_class, $object_permissions)) {
                $permission_class = $object_permissions[$object_class];
            } elseif ($object_permission_namespace) {
                //自动搜索
                $permission_class = $object_permission_namespace . join('', array_slice(explode('\\', $object_class), -1));
            } else {
                return false;
            }

            if (!class_exists($permission_class)) {
                return false;
            }

            $permission = new $permission_class();
            //前置检查
            if (method_exists($permission, 'before')) {
                $result = call_user_func([$permission, 'before'], $this, $action, $object);
                if (!is_null($result)) {
                    return (boolean) $result;
                }
            }

            if (!method_exists($permission, $action)) {
                return false;
            }

            return call_user_func([$permission, $action], $this, $object);

        } else {
            //直接检查角色里的权限列表定义
            return in_array($action, $this->getPermissions());
        }
    }
}