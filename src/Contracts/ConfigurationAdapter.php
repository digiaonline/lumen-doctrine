<?php

namespace Nord\Lumen\Doctrine\ORM\Contracts;

interface ConfigurationAdapter
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function map(array $config);
}
