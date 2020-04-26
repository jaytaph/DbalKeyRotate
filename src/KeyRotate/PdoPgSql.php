<?php

namespace KeyRotate;

use Doctrine\DBAL\Driver\PDOPgSql\Driver;

class PdoPgSql extends Driver
{
    use KeyrotateTrait;
}
