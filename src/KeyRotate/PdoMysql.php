<?php

namespace KeyRotate;

use Doctrine\DBAL\Driver\PDOMySql\Driver;

class PdoMysql extends Driver
{
    use KeyrotateTrait;
}
