<?php namespace Nord\Lumen\Doctrine\ORM\Console;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;

abstract class DoctrineSchemaCommand extends DoctrineCommand
{

    /**
     * @var SchemaTool
     */
    private $schemaTool;


    /**
     * DoctrineCommand constructor.
     *
     * @param SchemaTool    $schemaTool
     * @param EntityManager $entityManager
     */
    public function __construct(SchemaTool $schemaTool, EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->schemaTool = $schemaTool;
    }


    /**
     * @return SchemaTool
     */
    protected function getSchemaTool()
    {
        return $this->schemaTool;
    }
}
