<?php

namespace yunwuxin\auth\interfaces;

interface StatefulProvider extends Provider
{
    /**
     * 根据用户ID取得用户
     * @param $id
     * @return StatefulUser
     */
    public function retrieveById($id);

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return StatefulUser
     */
    public function retrieveByToken($id, $token);

    /**
     * 更新“记住我”的token
     * @param StatefulUser    $user
     * @param                 $token
     * @return mixed
     */
    public function updateRememberToken(StatefulUser $user, $token);

    /**
     * 验证密码
     * @param       $user
     * @param array $credentials
     * @return mixed
     */
    public function validateCredentials(StatefulUser $user, array $credentials);
}
