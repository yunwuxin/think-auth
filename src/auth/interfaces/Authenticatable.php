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

namespace think\auth\interfaces;

interface Authenticatable
{

    /**
     * 获取用户ID
     * @return mixed
     */
    public function getId();

    /**
     * 获取“记住我”令牌
     * @return mixed
     */
    public function getRememberToken();

    /**
     * 设置“记住我”令牌
     * @param $token
     * @return mixed
     */
    public function setRememberToken($token);

    /**
     * 根据用户ID取得用户
     * @param $id
     * @return mixed
     */
    public static function retrieveById($id);

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return mixed
     */
    public static function retrieveByToken($id, $token);

    /**
     * 根据用户输入的数据获取用户
     * @param array $credentials
     * @return mixed
     */
    public static function retrieveByCredentials(array $credentials);

    /**
     * 验证密码
     * @param       $user
     * @param array $credentials
     * @return mixed
     */
    public static function validateCredentials($user, array $credentials);

}