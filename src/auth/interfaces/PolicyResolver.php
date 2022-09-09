<?php

namespace yunwuxin\auth\interfaces;

interface PolicyResolver
{
    public function resolvePolicy($class);
}
