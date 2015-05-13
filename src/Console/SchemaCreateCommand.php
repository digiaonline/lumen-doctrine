<?php namespace Nord\Lumen\Doctrine\Console;

class SchemaCreateCommand extends DoctrineCommand
{

    /**
     * @var string
     */
    protected $name = 'doctrine:schema:create';

    /**
     * @var string
     */
    protected $description = 'Create database schema from entities';


    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function fire()
    {
        $tool     = $this->getSchemaTool();
        $metadata = $this->getClassMetadataFactory();

        $classes = $metadata->getAllMetadata();
        $sql     = $tool->getCreateSchemaSql($classes);

        if (count($sql) === 0) {
            $this->info('Nothing to create!');

            return;
        }

        $this->info('Creating database schema ...');

        $tool->createSchema($classes);

        if ($this->option('sql')) {
            $this->info(implode(';' . PHP_EOL, $sql));
        }

        $this->info('Schema created!');
    }
}
