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

namespace think\auth\traits;

use think\helper\Hash;

trait UserModel
{

    public function getAuthId()
    {
        return $this->data[$this->getPk()];
    }

    public function getRememberToken()
    {
        return $this->data[static::getRememberTokenName()];
    }

    public function setRememberToken($token)
    {
        $this->{static::getRememberTokenName()} = $token;

        $this->save();
    }

    protected static function getRememberTokenName()
    {
        return 'remember_token';
    }

    protected static function getPasswordName()
    {
        return 'password';
    }

    public static function retrieveById($id)
    {
        return self::get($id);
    }

    public static function retrieveByToken($id, $token)
    {
        return static::where(static::getDb()->getPk(), $id)
            ->where(static::getRememberTokenName(), $token)
            ->find();
    }

    public static function retrieveByCredentials(array $credentials)
    {
        $data = [];

        foreach ($credentials as $key => $value) {
            if (strpos($key, static::getPasswordName()) === false) {
                $data[$key] = $value;
            }
        }

        return static::where($data)->find();
    }

    public static function validateCredentials(self $user, array $credentials)
    {
        $plain = $credentials[self::getPasswordName()];

        return Hash::check($plain, $user->{self::getPasswordName()});
    }
}