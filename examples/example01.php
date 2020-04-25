<?php

use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\DBAL\DriverManager;

include "./vendor/autoload.php";

$connectionParams = [
    'dbname' => 'mysql',
    'user' => '/secret/database-2',         // Instead of username, use secretId
    'password' => '',                       // fetched from secret
    'host' => 'database-2.blaat.eu-west-1.rds.amazonaws.com',
    'driverClass' => KeyRotate\PdoMysql::class,
    'options' => [
        'cache' => new PhpFileCache(sys_get_temp_dir()),
    ]
];

$conn = DriverManager::getConnection($connectionParams);

while (true) {
    sleep(1);

    $result = $conn->query("SELECT 1")->execute();
    print_r($result);
}

