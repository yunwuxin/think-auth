<?php

namespace yunwuxin\auth\interfaces;

use yunwuxin\auth\credentials\BaseCredentials;

interface Provider
{
    /**
     * 根据用户输入的数据获取用户
     * @param BaseCredentials $credentials
     * @return mixed
     */
    public function retrieveByCredentials($credentials);
}
