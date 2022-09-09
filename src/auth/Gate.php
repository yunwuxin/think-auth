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

use Exception;
use ReflectionClass;
use think\App;
use think\Config;
use think\helper\Str;
use yunwuxin\Auth;
use yunwuxin\auth\interfaces\PolicyResolver;
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
                return new Role($role, [$role]);
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
     * @return bool
     */
    public function can($ability, ...$args)
    {
        return $this->raw($ability, ...$args) === true;
    }

    public function raw($ability, ...$args)
    {
        $user = $this->resolveUser();

        $object = $args[0] ?? $user;

        if (!is_null($policy = $this->getPolicyFor($object, $user))) {
            //前置检查
            $result = $this->callPolicyBefore(
                $policy, $user, $ability, $args
            );

            if (!is_null($result)) {
                return $result;
            }

            $method = $this->formatAbilityToMethod($ability);

            return $this->callPolicyMethod($policy, $method, $user, $args);
        }

        //直接检查角色里的权限列表定义
        return $this->hasPermission($ability, true);
    }

    protected function callPolicyMethod($policy, $method, $user, $arguments)
    {
        if (isset($arguments[0]) && is_string($arguments[0])) {
            array_shift($arguments);
        }

        if (!is_callable([$policy, $method])) {
            return;
        }

        if ($this->canBeCalledWithUser($user, $policy, $method)) {
            return $policy->{$method}($user, ...$arguments);
        }
    }

    protected function callPolicyBefore($policy, $user, $ability, $arguments)
    {
        if (!method_exists($policy, 'before')) {
            return;
        }

        if ($this->canBeCalledWithUser($user, $policy, 'before')) {
            return $policy->before($user, $ability, ...$arguments);
        }
    }

    protected function canBeCalledWithUser($user, $object, $method)
    {
        if (!is_null($user)) {
            return true;
        }

        try {
            $reflection = new ReflectionClass($object);

            $method = $reflection->getMethod($method);
        } catch (Exception $e) {
            return false;
        }

        if ($method) {
            $parameters = $method->getParameters();

            if (isset($parameters[0])) {
                $parameter = $parameters[0];
                return ($parameter->hasType() && $parameter->allowsNull()) ||
                    ($parameter->isDefaultValueAvailable() && is_null($parameter->getDefaultValue()));
            }
        }

        return false;
    }

    protected function formatAbilityToMethod($ability)
    {
        return strpos($ability, '-') !== false ? Str::camel($ability) : $ability;
    }

    protected function getPolicyFor($class, $user)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!is_string($class)) {
            return null;
        }

        if ($user instanceof PolicyResolver) {
            $policy = $user->resolvePolicy($class);
            if ($policy) {
                return $this->makePolicy($policy);
            }
        }

        if (isset($this->policies[$class])) {
            return $this->makePolicy($this->policies[$class]);
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->makePolicy($policy);
            }
        }

        if ($this->policyNamespace) {
            $class = $this->policyNamespace . class_basename($class) . 'Policy';
            return $this->makePolicy($class);
        }
    }

    protected function makePolicy($class)
    {
        return $this->app->make($class);
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
