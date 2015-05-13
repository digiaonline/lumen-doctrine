<?php namespace Nord\Lumen\Doctrine\Console;

class SchemaDropCommand extends DoctrineCommand
{

    /**
     * @var string
     */
    protected $name = 'doctrine:schema:drop';

    /**
     * @var string
     */
    protected $description = 'Drop database schema';


    /**
     * @inheritdoc
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function fire()
    {
        $tool     = $this->getSchemaTool();
        $metadata = $this->getClassMetadataFactory();

        $classes = $metadata->getAllMetadata();
        $sql     = $tool->getDropSchemaSQL($classes);

        if (count($sql) === 0) {
            $this->info('Nothing to drop!');

            return;
        }

        $this->info('Dropping database schema ...');

        $tool->dropSchema($classes);

        if ($this->option('sql')) {
            $this->info(implode(';' . PHP_EOL, $sql));
        }

        $this->info('Schema dropped!');
    }
}
