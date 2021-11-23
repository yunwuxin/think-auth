<?php

namespace yunwuxin\auth\interfaces;

interface Provider
{
    /**
     * 根据用户输入的数据获取用户
     * @param mixed $credentials
     * @return mixed
     */
    public function retrieveByCredentials($credentials);
}
