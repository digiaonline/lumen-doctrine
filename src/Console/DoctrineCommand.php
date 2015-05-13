<?php namespace Nord\Lumen\Doctrine\Console;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Illuminate\Console\Command;

abstract class DoctrineCommand extends Command
{

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var ClassMetadataFactory
     */
    private $classMetadataFactory;


    /**
     * DoctrineCommand constructor.
     *
     * @param SchemaTool           $schemaTool
     * @param ClassMetadataFactory $classMetadataFactory
     */
    public function __construct(SchemaTool $schemaTool, ClassMetadataFactory $classMetadataFactory)
    {
        parent::__construct();

        $this->schemaTool           = $schemaTool;
        $this->classMetadataFactory = $classMetadataFactory;
    }


    /**
     * @return SchemaTool
     */
    protected function getSchemaTool()
    {
        return $this->schemaTool;
    }


    /**
     * @return ClassMetadataFactory
     */
    protected function getClassMetadataFactory()
    {
        return $this->classMetadataFactory;
    }
}
