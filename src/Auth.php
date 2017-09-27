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

use InvalidArgumentException;
use think\facade\Config;
use think\helper\Str;
use yunwuxin\auth\Guard;
use yunwuxin\auth\guard\Session;
use yunwuxin\auth\guard\Token;
use yunwuxin\auth\interfaces\StatefulGuard;
use yunwuxin\auth\Provider;

/**
 * Class Auth
 * @package yunwuxin
 *
 * @mixin Session
 * @mixin Token
 */
class Auth
{
    private static $instance;

    /** @var Guard[] */
    protected $guards = [];

    protected function buildGuard($name)
    {
        $className = false !== strpos($name, '\\') ? $name : "\\yunwuxin\\auth\\guard\\" . Str::studly($name);
        if (class_exists($className)) {
            return new $className($this->buildProvider());
        }
        throw new InvalidArgumentException("Auth guard driver [{$name}] is not defined.");
    }

    /**
     * @param null $name
     * @return mixed|Guard|StatefulGuard
     */
    public function guard($name = null)
    {
        $name = $name ?: Config::get('auth.guard');

        return isset($this->guards[$name])
        ? $this->guards[$name]
        : $this->guards[$name] = $this->buildGuard($name);
    }

    /**
     * @param null $provider
     * @return Provider
     */
    public function buildProvider($provider = null)
    {
        $config = Config::get('auth.provider');

        $provider = $provider ?: $config['type'];

        $className = false !== strpos($provider, '\\') ? $provider : "\\yunwuxin\\auth\\provider\\" . Str::studly($provider);

        if (class_exists($className)) {
            return new $className($config);
        }

        throw new InvalidArgumentException("Authentication user provider [{$config['type']}] is not defined.");
    }

    /**
     * 获取实例
     * @return Auth
     */
    public static function instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->guard(), $method], $parameters);
    }

}
