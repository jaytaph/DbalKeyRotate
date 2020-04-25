<?php

namespace KeyRotate;

use Aws\SecretsManager\SecretsManagerClient;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Driver\PDOMySql\Driver;

class BaseDriver extends Driver
{

    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (! isset($params['options']['cache'])) {
            throw DBALException::cacheParamNotSet();
        }

        $cache = $params['options']['cache'];
        if (! $cache instanceof Cache) {
            throw DBALException::invalidCacheClass();
        }

        $retries = 1;
        while ($retries) {
            try {
                list($user, $pass) = $this->fetchCredentials($username);
                $conn = parent::connect($params, $user, $pass, $driverOptions);
                return $conn;
            } catch (\Exception $e) {
                $lastException = $e;
                $retries--;
            }
        }

        throw $lastException;
    }


    protected function fetchCredentials($secretId)
    {
        $options = [];
        $options['region'] = 'eu-west-1';
        $options['version'] = 'latest';
        $options['profile'] = 'secrets';

        $client = new SecretsManagerClient($options);
        $result = $client->getSecretValue([
            'SecretId' => $secretId,
        ]);
        if (is_array($result) || ! isset($result['SecretString'])) {
            return array("", "");
        }

        $result = json_decode($result['SecretString'], true);
        return array(
            $result['username'] ?? "",
            $result['password'] ?? ""
        );
    }

}
