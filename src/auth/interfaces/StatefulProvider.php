<?php

namespace yunwuxin\auth\interfaces;

interface StatefulProvider extends Provider
{

    /**
     * 获取用户ID
     * @param $user mixed
     * @return mixed
     */
    public function getId($user);

    /**
     * 获取“记住我”令牌
     * @param $user mixed
     * @return string
     */
    public function getRememberToken($user);

    /**
     * 设置“记住我”令牌
     * @param $user mixed
     * @param $token string
     * @return void
     */
    public function setRememberToken($user, $token);

    /**
     * 根据用户ID取得用户
     * @param $id
     * @return mixed
     */
    public function retrieveById($id);

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return mixed
     */
    public function retrieveByToken($id, $token);

}
