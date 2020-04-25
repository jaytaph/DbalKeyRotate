<?php

namespace KeyRotate;

use Doctrine\DBAL\DBALException as BaseDBALException;

class DBALException extends BaseDBALException
{
    public static function cacheParamNotSet()
    {
        return new self(sprintf("The options[cache] is not set"));
    }

    public static function invalidCacheClass()
    {
        return new self(sprintf("The given cache class does not implement Doctrine\Cache"));
    }
}

