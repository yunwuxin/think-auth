<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yunwuxin\auth;

use think\Config;
use think\helper\Str;
use yunwuxin\auth\traits\AuthorizableUser as User;

class Gate
{
    /** @var User */
    protected $user;

    protected $policies;

    protected $policyNamespace;

    protected static $instance = [];

    public function __construct($policies = [], $policyNamespace = null)
    {
        $this->policies        = (array) $policies;
        $this->policyNamespace = $policyNamespace;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 是否具有某个角色
     *
     * @param array|string $name
     * @param bool         $requireAll
     * @return bool
     */
    public function hasRole($name, $requireAll = false)
    {
        if (!$this->user) {
            return false;
        }
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
            foreach ($this->user->getRoles() as $role) {
                if ($role->getName() == $name) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * 获取用户的所有权限
     *
     * @return array
     */
    public function getPermissions()
    {
        if (!$this->user) {
            return [];
        }
        $roles       = $this->user->getRoles();
        $permissions = [];
        array_map(function (Role $role) use (&$permissions) {
            $permissions = array_merge($permissions, $role->getPermissions());
        }, $roles);
        return $permissions;
    }

    /**
     * 是否具有某个权限
     *
     * @param      $name
     * @param bool $requireAll
     * @return bool
     */
    public function hasPermission($name, $requireAll = false)
    {
        if (!$this->user) {
            return false;
        }
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
     *
     * @param       $ability
     * @param array $args
     * @return bool|mixed
     */
    public function can($ability, ...$args)
    {
        if (isset($args[0])) {
            if (!is_null($policy = $this->getPolicyFor($args[0]))) {

                //前置检查
                if (method_exists($policy, 'before')) {
                    $result = $policy->before($this->user, $ability, ...$args);
                    if (!is_null($result)) {
                        return $result;
                    }
                }

                $ability = $this->formatAbilityToMethod($ability);

                if (isset($args[0]) && is_string($args[0])) {
                    array_shift($args);
                }

                return is_callable([$policy, $ability])
                    ? $policy->{$ability}($this->user, ...$args)
                    : false;
            }
        }

        //直接检查角色里的权限列表定义
        return $this->hasPermission($ability, true);

    }

    protected function formatAbilityToMethod($ability)
    {
        return strpos($ability, '-') !== false ? Str::camel($ability) : $ability;
    }

    protected function getPolicyFor($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!is_string($class)) {
            return null;
        }

        if (isset($this->policies[$class])) {
            return $this->resolvePolicy($this->policies[$class]);
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }

        if ($this->policyNamespace) {
            $class = $this->policyNamespace . join('', array_slice(explode('\\', $class), -1)) . 'Policy';
            return $this->resolvePolicy($class);
        }
    }

    protected function resolvePolicy($class)
    {
        static $policies;
        if (!isset($policies[$class])) {
            $policies[$class] = new $class;
        }
        return $policies[$class];
    }

    /**
     * @param $user
     * @return static
     */
    public function forUser($user)
    {
        return (new static($this->policies, $this->policyNamespace))->setUser($user);
    }


    public static function __make(Config $config)
    {
        $policies        = $config->get('auth.policies');
        $policyNamespace = $config->get('auth.policy_namespace');

        return new self($policies, $policyNamespace);
    }

}
