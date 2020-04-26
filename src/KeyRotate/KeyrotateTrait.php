<?php

namespace KeyRotate;

use Aws\SecretsManager\SecretsManagerClient;
use Doctrine\Common\Cache\Cache;

trait KeyrotateTrait
{
    /**
     * @param array $params
     * @param null $username
     * @param null $password
     * @param array $driverOptions
     * @return mixed
     * @throws DBALException
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (! isset($params['options']['cache'])) {
            throw DBALException::optionNotSet('cache');
        }

        if (! isset($params['options']['secretId'])) {
            throw DBALException::optionNotSet('secretId');
        }

        $cache = $params['options']['cache'];
        if (! $cache instanceof Cache) {
            throw DBALException::invalidCacheClass();
        }

        $cacheId = $params['host'];

        $retries = $params['options']['retries'] ?? 5;
        while ($retries > 0) {
            try {
                if (! $cache->contains($cacheId)) {
                    // Try local username and password when set and no cache is stored
                    if (! empty($username)) {
                        $user = $username;
                        $pass = $password;
                    } else {
                        // No cache found and no local username, fetch credentials from AWS
                        list($user, $pass) = $this->fetchCredentialsFromAWS(
                            $params['options']['secretId'],
                            $params['options']['awsOptions'] ?? []
                        );
                        $cache->save($cacheId, array($user, $pass));
                    }
                } else {
                    list($user, $pass) = $cache->fetch($cacheId);
                }

                $conn = parent::connect($params, $user, $pass, $driverOptions);
                return $conn;
            } catch (\Exception $e) {
                // Clear username so we don't try local again
                $username = '';
                $lastException = $e;
                $retries--;
            }
        }

        throw $lastException;
    }

    /**
     * @param string $secretId
     * @param array $options
     * @return array
     */
    protected function fetchCredentialsFromAWS(string $secretId, array $options): array
    {
        $options = array_merge(['version' => 'latest'], $options);

        $client = new SecretsManagerClient($options);
        $result = $client->getSecretValue([
            'SecretId' => $secretId,
        ]);

        // @TODO: also check for binary results
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
