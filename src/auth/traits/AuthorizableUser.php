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

namespace think\auth\traits;

use think\auth\Role;
use think\Config;

trait AuthorizableUser
{
    /**
     * 获取用户角色
     * @return Role
     */
    public function getRole()
    {
        throw new \LogicException('You must override the getRole() method in the concrete user class.');
    }

    /**
     * 是否为超级管理员[拥有所有的权限]
     * @return boolean
     */
    public function isSuperAdmin()
    {
        return false;
    }

    /**
     * 检查权限
     * @param      $action
     * @param null $object
     * @return bool|mixed
     */
    public function can($action, $object = null)
    {
        $role               = $this->getRole();
        $permissions        = (array) $role->getPermissions();
        $object_permissions = Config::get('auth.object_permissions', []);
        if (!is_null($object)) {
            if (is_string($object)) {
                //直接传入类名的情况
                $object_class = $object;
                $object       = null;
            } else {
                $object_class = get_class($object);
            }
            if (!array_key_exists($object_class, $object_permissions)) {
                return false;
            }
            $permission_class = $object_permissions[$object_class];
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
            return in_array($action, $permissions);
        }
    }
}