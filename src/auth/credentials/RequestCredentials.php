<?php

namespace yunwuxin\auth\credentials;

use think\Request;

class RequestCredentials extends BaseCredentials
{
    public function __construct(Request $request)
    {
        parent::__construct(['request' => $request]);
    }

    /**
     * @return Request|\app\Request
     */
    public function getRequest()
    {
        return $this->offsetGet('request');
    }
}
