<?php

namespace yunwuxin\auth\exception;

use Exception;
use think\exception\HttpException;

class UnauthorizedHttpException extends HttpException
{
    public function __construct(string $challenge, string $message = null, Exception $previous = null, ?int $code = 0, array $headers = [])
    {
        $headers['WWW-Authenticate'] = $challenge;

        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
