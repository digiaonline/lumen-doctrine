<?php

namespace Nord\Lumen\Doctrine\ORM\Console;

use Symfony\Component\Console\Input\InputOption;

class SchemaUpdateCommand extends DoctrineSchemaCommand
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
     * {@inheritdoc}
     */
    public function fire()
    {
        $tool = $this->getSchemaTool();
        $metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        $sql = $tool->getUpdateSchemaSql($metadata);

        if (count($sql) === 0) {
            $this->info('Nothing to update!');

            return;
        }

        $this->info('Updating database schema ...');

        $tool->updateSchema($metadata);

        if ($this->option('sql')) {
            $this->info(implode(';'.PHP_EOL, $sql));
        }

        $this->info('Schema updated!');
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['sql', false, InputOption::VALUE_NONE, 'Dumps SQL queries.'],
        ];
    }
}
