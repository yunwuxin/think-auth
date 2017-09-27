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
namespace yunwuxin\auth\password;

use think\facade\Cache;
use think\helper\Str;
use yunwuxin\auth\interfaces\CanResetPassword;

class Token
{

    public static function create(CanResetPassword $user)
    {
        $email = $user->getEmailForResetPassword();

        $token = self::createNewToken();

        Cache::set(self::getCacheKey($user), self::getPayload($email, $token));

        return $token;
    }

    protected static function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => $token, 'create_time' => time()];
    }

    protected static function getCacheKey(CanResetPassword $user)
    {
        return 'password:reset:' . md5($user->getEmailForResetPassword());
    }

    protected static function createNewToken()
    {
        return sha1(Str::random(40));
    }

    public static function exists(CanResetPassword $user, $token)
    {
        $tokenCache = Cache::get(self::getCacheKey($user));

        return $tokenCache && $tokenCache['token'] == $token && $tokenCache['create_time'] + 30 * 60 > time();
    }

    public static function delete(CanResetPassword $user)
    {
        Cache::rm(self::getCacheKey($user));
    }

}
