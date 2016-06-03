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

namespace think\auth;


use think\Db;
use think\helper\Hash;
use think\Model;

class UserProvider
{
    /**
     * @var String
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param $identifier
     * @return Authenticatable
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->db()->find($identifier);
    }

    /**
     * @param $identifier
     * @param $token
     * @return Authenticatable
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->db()
            ->where($model->getKeyName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->find();
    }

    /**
     * @param Authenticatable|Model $user
     * @param                       $token
     */
    public function updateRememberToken($user, $token)
    {
        $user->setRememberToken($token);

        $user->save();
    }

    /**
     * @param array $credentials
     * @return Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->createModel()->db();

        foreach ($credentials as $key => $value) {
            if (strpos($key, 'password') === false) {
                $query->where($key, $value);
            }
        }

        return $query->find();
    }

    /**
     * @param Authenticatable $user
     * @param array           $credentials
     * @return mixed
     */
    public function validateCredentials($user, array $credentials)
    {
        $plain = $credentials['password'];

        return Hash::check($plain, $user->getAuthPassword());
    }

    /**
     * @return Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

}