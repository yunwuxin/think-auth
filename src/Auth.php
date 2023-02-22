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

namespace yunwuxin;

use Closure;
use InvalidArgumentException;
use think\helper\Arr;
use think\helper\Str;
use think\Manager;
use yunwuxin\auth\guard\Session;
use yunwuxin\auth\guard\Token;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\StatefulGuard;

/**
 * Class Auth
 * @package yunwuxin
 * @mixin Session
 * @mixin Token
 */
class Auth extends Manager
{
    protected $namespace = '\\yunwuxin\\auth\\guard\\';

    protected $default = null;
    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    public function shouldUse($name)
    {
        $this->default = $name;
        return $this;
    }

    /**
     * @param null $name
     * @return Guard|StatefulGuard|Session|Token
     */
    public function guard($name = null)
    {
        return $this->driver($name);
    }

    /**
     * 获取配置
     * @param null|string $name 名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('auth.' . $name, $default);
        }

        return $this->app->config->get('auth');
    }

    /**
     * 获取guard配置
     * @param string $guard
     * @param string|null $name
     * @param null $default
     * @return mixed
     */
    public function getGuardConfig(string $guard, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("guards.{$guard}")) {
            return Arr::get($config, $name, $default);
        }

        throw new InvalidArgumentException("Guard [$guard] not found.");
    }

    /**
     * 获取provider配置
     * @param string $provider
     * @param string|null $name
     * @param null $default
     * @return mixed
     */
    public function getProviderConfig(string $provider, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("providers.{$provider}")) {
            return Arr::get($config, $name, $default);
        }

        throw new InvalidArgumentException("Provider [$provider] not found.");
    }

    /**
     * 获取驱动类型
     * @param string $name
     * @return mixed
     */
    protected function resolveType(string $name)
    {
        return $this->getGuardConfig($name, 'type');
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return mixed
     */
    protected function resolveConfig(string $name)
    {
        return $this->getGuardConfig($name);
    }

    protected function resolveParams($name): array
    {
        $config = $this->resolveConfig($name);

        $providerName = $this->getGuardConfig($name, 'provider');

        $provider = $this->createUserProvider($providerName);

        return [$provider, $config];
    }

    public function createUserProvider($provider)
    {
        $config = $this->getProviderConfig($provider);

        $type = Arr::pull($config, 'type');

        $namespace = '\\yunwuxin\\auth\\provider\\';

        $class = false !== strpos($type, '\\') ? $type : $namespace . Str::studly($type);

        if (class_exists($class)) {
            return $this->app->invokeClass($class, [$config]);
        }

        throw new InvalidArgumentException("Provider [$type] not supported.");
    }

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->default ?? $this->getConfig('default');
    }

    /**
     * 创建驱动
     *
     * @param string $name
     * @return mixed
     *
     */
    protected function createDriver(string $name)
    {
        $driver = $this->resolveType($name);
        if (is_null($driver)) {
            throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }
        $params = $this->resolveParams($name);

        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver, $params);
        }
        $method = 'create' . Str::studly($driver) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        }
        $class = $this->resolveClass($driver);

        return $this->app->invokeClass($class, $params);
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @return mixed
     */
    protected function callCustomCreator($driver, array $params)
    {
        return $this->customCreators[$driver]($this->app, $driver, $params);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }
}
