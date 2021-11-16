<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace yunwuxin\auth\provider;

use think\helper\Arr;
use yunwuxin\auth\interfaces\StatefulProvider;
use yunwuxin\auth\model\User;

class Model implements StatefulProvider
{

    protected $model;
    protected $fields = [
        'password'       => 'password',
        'remember_token' => 'remember_token',
    ];

    public function __construct($config)
    {
        $this->model  = Arr::get($config, 'model', User::class);
        $this->fields = array_merge($this->fields, Arr::get($config, 'fields', []));
    }

    protected function getFieldName($name)
    {
        return Arr::get($this->fields, $name);
    }

    /**
     * @param \think\Model $user
     * @return mixed
     */
    public function getId($user)
    {
        return $user->getAttr($user->getPk());
    }

    /**
     * @param \think\Model $user
     * @return string
     */
    public function getRememberToken($user)
    {
        return $user->getAttr($this->getFieldName('remember_token'));
    }

    /**
     * @param \think\Model $user
     * @param string $token
     * @return void
     */
    public function setRememberToken($user, $token)
    {
        $user->setAttr($this->getFieldName('remember_token'), $token);
        $user->save();
    }

    /**
     * 根据用户ID取得用户
     * @param $id
     * @return mixed
     */
    public function retrieveById($id)
    {
        return $this->createModel()->find($id);
    }

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return mixed
     */
    public function retrieveByToken($id, $token)
    {
        $model = $this->createModel();

        return $model->where($model->getPk(), $id)
                     ->where($model->getRememberTokenName(), $token)
                     ->find();
    }

    /**
     * 根据用户输入的数据获取用户
     * @param array $credentials
     * @return mixed
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $data = [];

        foreach ($credentials as $key => $value) {
            if (strpos($key, 'password') === false) {
                $data[$key] = $value;
            }
        }

        $user = $this->createModel()->where($data)->find();

        if (
            $user
            && isset($credentials['password'])
            && password_verify($credentials['password'], $user->getAttr($this->getFieldName('password')))
        ) {
            return $user;
        }

        return null;
    }

    protected function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }
}
