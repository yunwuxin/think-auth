# ThinkPHP6 权限认证

## 安装
~~~
composer require yunwuxin/think-auth
~~~

## 使用

app\model\User 继承 yunwuxin\auth\model\User 或者实现对应接口；

app\Request 增加

```php
    /**
     * The user resolver callback.
     *
     * @var Closure
     */
    protected $userResolver;

    /**
     * Get the user resolver callback.
     *
     * @return Closure
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?: function () {
            //
        };
    }

    /**
     * Set the user resolver callback.
     *
     * @param Closure $callback
     * @return $this
     */
    public function setUserResolver(Closure $callback): Request
    {
        $this->userResolver = $callback;
        return $this;
    }

    /**
     * Get the user making the request.
     *
     * @param string|null $guard
     * @return mixed
     */
    public function user(string $guard = null)
    {
        return call_user_func($this->getUserResolver(), $guard);
    }
```

BaseController 增加

```php
    // 初始化
    protected function initialize()
    {
        $this->request->setUserResolver(function ($guard = null) {
            return Auth::guard($guard)->user();
        });
    }
```

接着控制器里就可以获取用户状态了

```php
var_dump($this->request->user());
```