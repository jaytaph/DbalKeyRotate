DBAL driver for automatic retrieval of credentials through AWS secretsmanager.

> This is a highly experimental POC library. Do not use in production!

This library will automatically fetch credentials from the AWS secrets manager. The secrets manager allows easy 
key rotating, and this library will fetch any new credentials without any changes your code or reboots of your 
instance, pod or container. 

Because key retrieval is a time-consuming operation, the fetched credentials are stored inside a cache. The driver 
will first try the cached credentials and only when these credentials fail to connect, it will fetch credentials 
from the secrets manager. After a specific amount of attempt, the driver will fail if no correct credential can be 
retrieved.

If you have set a local username and password and no cached credentials are present, these local credentials will 
be tried first. If you do not need this behaviour, you can leave the username and password empty.



# Usage

    $connectionParams = [
        'dbname' => 'mysql',
        'user' => '',
        'password' => '',              
        'host' => 'mydb.1234.eu-west-1.rds.amazonaws.com',
        'driverClass' => KeyRotate\Driver::class,
        'options' => [
            'cache' => new PhpFileCache(sys_get_temp_dir()),
            'secretId' => /secret/database-2
            'retries' => 5,
            'awsOptions' => [],
        ]
    ];

The driver class needs a few options in order to work:

cache
    The drivers needs an mandatory cache service in order to function. This is a class that implements Doctrine\Cache.

secretId
    The actual secret that must be retrieved.

retries
    The number of tries of database connection and key fetching that must be done before giving up.
   
awsOptions
    An array of AWS options send to the AWS client. This chould consist of region, profile etc. Probably not needed
    when running on an ecs/ec2 instance where AWS credentials and information are automatically fetched by the AWS 
    client.
