<?php namespace Nord\Lumen\Doctrine\Console;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Illuminate\Console\Command;

abstract class DoctrineCommand extends Command
{

    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * DoctrineCommand constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }


    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}
