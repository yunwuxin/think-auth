<?php

namespace yunwuxin\auth\guard;

use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\auth\traits\GuardHelpers;

abstract class Password implements Guard
{
    use GuardHelpers;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }
}
