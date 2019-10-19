<?php

namespace yunwuxin\auth\interfaces;

interface Provider
{
    /**
     * 根据用户输入的数据获取用户
     * @param array $credentials
     * @return mixed
     */
    public function retrieveByCredentials(array $credentials);
}
