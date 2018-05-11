<?php

namespace yunwuxin\auth\behavior;

use think\exception\HttpResponseException;

class AuthenticationWithBasicAuth
{
    public function run()
    {
        if ($response = auth()->basic()) {
            throw new HttpResponseException($response);
        }
    }
}