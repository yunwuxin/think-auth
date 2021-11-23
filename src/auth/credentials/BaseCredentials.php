<?php

namespace yunwuxin\auth\credentials;

use ArrayObject;
use ReflectionClass;

class BaseCredentials extends ArrayObject
{

    /**
     * @param array $credentials
     * @return static
     */
    public static function fromArray(array $credentials = [])
    {
        $reflect = new ReflectionClass(static::class);

        /** @var static $object */
        $object = $reflect->newInstanceWithoutConstructor();

        $object->exchangeArray($credentials);

        return $object;
    }

}
