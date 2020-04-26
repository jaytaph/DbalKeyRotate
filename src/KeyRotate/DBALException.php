<?php

namespace KeyRotate;

use Doctrine\DBAL\DBALException as BaseDBALException;

class DBALException extends BaseDBALException
{

    public static function invalidCacheClass()
    {
        return new self(sprintf("The given cache class does not implement Doctrine\Cache"));
    }

    public static function optionNotSet(string $option)
    {
        return new self(sprintf("The option '%s' is not set in the options array", $option));
    }
}

