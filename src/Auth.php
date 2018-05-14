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
use think\App;
use think\Config;
use think\Factory;
use think\helper\Str;
use yunwuxin\auth\Guard;
use yunwuxin\auth\guard\Session;
use yunwuxin\auth\guard\Token;
use yunwuxin\auth\interfaces\StatefulGuard;
use yunwuxin\auth\Provider;

/**
 * Class Auth
 *
 * @package yunwuxin
 * @mixin Session
 * @mixin Token
 */
class Auth
{

    use Factory;

    /** @var Guard[] */
    protected $guards = [];

    protected $config;

    /** @var App */
    protected $app;

    public function __construct($config, $app)
    {
        $this->config = $config;
        $this->app    = $app;
    }

    public function shouldUse($name)
    {
        $this->config['guard'] = $name;
        return $this;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->guard(), $method], $parameters);
    }

    /**
     * @param null $name
     * @return mixed|Guard|StatefulGuard|Session|Token
     */
    public function guard($name = null)
    {
        $name = $name ?: $this->config['guard'];

        return isset($this->guards[$name])
            ? $this->guards[$name]
            : $this->guards[$name] = $this->buildGuard($name);
    }

    protected function buildGuard($name)
    {
        $className = false !== strpos($name, '\\') ? $name : "\\yunwuxin\\auth\\guard\\" . Str::studly($name);
        if (class_exists($className)) {
            return $this->app->container()->make($className, [$this->buildProvider()]);
        }
        throw new InvalidArgumentException("Auth guard driver [{$name}] is not defined.");
    }


    /**
     * @param null $provider
     * @return Provider
     */
    public function buildProvider($provider = null)
    {
        $config = $this->config['provider'];

        $provider = $provider ?: $config['type'];

        $className = false !== strpos($provider, '\\') ? $provider : "\\yunwuxin\\auth\\provider\\" . Str::studly($provider);

        if (class_exists($className)) {
            return new $className($config);
        }

        throw new InvalidArgumentException("Authentication user provider [{$config['type']}] is not defined.");
    }


    public static function __make(Config $config, App $app)
    {
        return new self($config->get('auth'), $app);
    }
}