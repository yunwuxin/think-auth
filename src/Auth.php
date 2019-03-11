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

use think\App;
use think\Config;
use yunwuxin\auth\Guard;
use yunwuxin\auth\guard\Session;
use yunwuxin\auth\guard\Token;
use yunwuxin\auth\interfaces\StatefulGuard;

/**
 * Class Auth
 * @package yunwuxin
 * @mixin Session
 * @mixin Token
 */
class Auth
{
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
        return App::factory($name, "\\yunwuxin\\auth\\guard\\", $this->buildProvider());
    }


    public function buildProvider($provider = null)
    {
        $config = $this->config['provider'];

        $provider = $provider ?: $config['type'];

        return App::factory($provider, "\\yunwuxin\\auth\\provider\\", $config);
    }


    public static function __make(Config $config, App $app)
    {
        return new self($config->get('auth'), $app);
    }
}