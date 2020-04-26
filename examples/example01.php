<?php

use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\DBAL\DriverManager;

include "../vendor/autoload.php";

$connectionParams = [
    'dbname' => 'db',
    'user' => '',
    'password' => '',
    'host' => 'my-rds-instance.12345678.eu-west-1.rds.amazonaws.com',
    'driverClass' => KeyRotate\PdoMysql::class,
    'options' => [
        'cache' => new PhpFileCache(sys_get_temp_dir()),
        'secretId' => '/secret/my-rds-instance-credentials',
        'retries' => 10,
        'awsOptions' => [
            'region' => 'eu-west-1',
        ]        
    ]
];

$conn = DriverManager::getConnection($connectionParams);

while (true) {
    sleep(1);

    $result = $conn->query("SELECT 1")->execute();
    print_r($result);
}

