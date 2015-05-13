<?php namespace Nord\Lumen\Doctrine\Console;

class SchemaUpdateCommand extends DoctrineCommand
{

    /**
     * @var string
     */
    protected $name = 'doctrine:schema:update';

    /**
     * @var string
     */
    protected $description = 'Update database schema to match entities';


    /**
     * @inheritdoc
     */
    public function fire()
    {
        $tool     = $this->getSchemaTool();
        $metadata = $this->getClassMetadataFactory();

        $classes = $metadata->getAllMetadata();

        $sql = $tool->getUpdateSchemaSql($metadata->getAllMetadata());

        if (count($sql) === 0) {
            $this->info('Nothing to update!');

            return;
        }

        $this->info('Updating database schema ...');

        $tool->updateSchema($classes);

        if ($this->option('sql')) {
            $this->info(implode(';' . PHP_EOL, $sql));
        }

        $this->info('Schema updated!');
    }
}
