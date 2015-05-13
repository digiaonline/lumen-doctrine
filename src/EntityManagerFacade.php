<?php namespace Nord\Lumen\Doctrine;

use Illuminate\Support\Facades\Facade;

class EntityManagerFacade extends Facade
{

    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'Doctrine\ORM\EntityManager';
    }
}
