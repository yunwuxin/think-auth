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

use think\App;
use think\Config;
use think\helper\Str;
use yunwuxin\Auth;
use yunwuxin\auth\traits\AuthorizableUser as User;

class Gate
{
    protected $app;

    /**
     * @var callable
     */
    protected $userResolver;

    protected $policies;

    protected $policyNamespace;

    protected static $instance = [];

    public function __construct(App $app, callable $userResolver, array $policies = [], string $policyNamespace = null)
    {
        $this->app             = $app;
        $this->userResolver    = $userResolver;
        $this->policies        = $policies;
        $this->policyNamespace = $policyNamespace;
    }

    /**
     * @param User $user
     * @return Role[]
     */
    protected function getRoles($user)
    {
        $roles = (array) $user->getRoles();

        return array_map(function ($role) {
            if (is_string($role)) {
                return new Role($role);
            }
            return $role;
        }, $roles);
    }

    /**
     * 是否具有某个角色
     *
     * @param array|string $name
     * @param bool $requireAll
     * @return bool
     */
    public function hasRole($name, $requireAll = false)
    {
        $user = $this->resolveUser();

        if (!$user) {
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
            foreach ($this->getRoles($user) as $role) {
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
        $user = $this->resolveUser();

        if (!$user) {
            return [];
        }

        $roles = $this->getRoles($user);

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
        return $this->raw($ability, ...$args) === true;
    }

    public function raw($ability, ...$args)
    {
        if (isset($args[0])) {
            if (!is_null($policy = $this->getPolicyFor($args[0]))) {
                $user = $this->resolveUser();
                //前置检查
                if (method_exists($policy, 'before')) {
                    $result = $policy->before($user, $ability, ...$args);
                    if (!is_null($result)) {
                        return $result;
                    }
                }

                $ability = $this->formatAbilityToMethod($ability);

                if (is_string($args[0])) {
                    array_shift($args);
                }

                return is_callable([$policy, $ability])
                    ? $policy->{$ability}($user, ...$args)
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
     * @return User
     */
    protected function resolveUser()
    {
        return call_user_func($this->userResolver);
    }

    /**
     * @param $user
     * @return static
     */
    public function forUser($user)
    {
        return new static($this->app, function () use ($user) {
            return $user;
        }, $this->policies, $this->policyNamespace);
    }

    public static function __make(App $app, Config $config, Auth $auth)
    {
        $policies        = $config->get('auth.policies');
        $policyNamespace = $config->get('auth.policy_namespace');

        return new self($app, function () use ($auth) {
            return $auth->guard()->user();
        }, $policies, $policyNamespace);
    }

}
