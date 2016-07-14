<?php

namespace Nord\Lumen\Doctrine\ORM\Configuration;

use Nord\Lumen\Doctrine\ORM\Contracts\ConfigurationAdapter;

class ConnectionConfiguration
{
    /**
     * @var ConfigurationAdapter
     */
    private $adapter;

    /**
     * ConnectionConfiguration constructor.
     *
     * @param ConfigurationAdapter $adapter
     */
    public function __construct(ConfigurationAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function map(array $config)
    {
        return $this->adapter->map($config);
    }
}
