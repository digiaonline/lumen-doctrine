<?php namespace Nord\Lumen\Doctrine\Facades;

use Illuminate\Support\Facades\Facade;

class EntityManager extends Facade
{

    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'Doctrine\ORM\EntityManagerInterface';
    }
}
