<?php

namespace Nord\Lumen\Doctrine\ORM\Configuration;

use Nord\Lumen\Doctrine\ORM\Contracts\ConfigurationAdapter as ConfigurationAdapterContract;

class SqliteAdapter implements ConfigurationAdapterContract
{
    /**
     * {@inheritdoc}
     */
    public function map(array $config)
    {
        $array = [
            'driver'   => 'pdo_sqlite',
            'user'     => array_get($config, 'username'),
            'password' => array_get($config, 'password'),
            'prefix'   => array_get($config, 'prefix'),
        ];

        if ($config['database'] === ':memory:') {
            $array['memory'] = true;
        } else {
            $array['path'] = $config['database'];
        }

        return $array;
    }
}
