<?php namespace Nord\Lumen\Doctrine\ORM\Configuration;

use Nord\Lumen\Doctrine\ORM\Contracts\ConfigurationAdapter as ConfigurationAdapterContract;

class SqlAdapter implements ConfigurationAdapterContract
{

    /**
     * @inheritdoc
     */
    public function map(array $config)
    {
        return [
            'driver'   => $this->normalizeDriver($config['driver']),
            'host'     => $config['host'],
            'port'     => $config['port'],
            'dbname'   => $config['database'],
            'user'     => $config['username'],
            'password' => $config['password'],
            'charset'  => $config['charset'],
            'prefix'   => array_get($config, 'prefix'),
        ];
    }


    /**
     * @param $driver
     *
     * @return string
     */
    private function normalizeDriver($driver)
    {
        $driverMap = [
            'mysql'  => 'pdo_mysql',
            'pgsql'  => 'pdo_pgsql',
            'sqlsrv' => 'pdo_sqlsrv',
        ];

        return $driverMap[$driver];
    }
}
