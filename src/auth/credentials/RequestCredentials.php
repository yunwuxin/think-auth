<?php

namespace yunwuxin\auth\credentials;

use think\helper\Str;
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

    public function getToken()
    {
        $request = $this->getRequest();
        $token   = $request->param('access-token');
        if (empty($token)) {
            $header = $request->header('Authorization');
            if (!empty($header)) {
                if (Str::startsWith($header, 'Bearer ')) {
                    $token = Str::substr($header, 7);
                }
            }
        }

        return $token;
    }
}
