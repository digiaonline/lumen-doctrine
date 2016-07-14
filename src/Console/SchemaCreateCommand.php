<?php

namespace Nord\Lumen\Doctrine\ORM\Console;

use Symfony\Component\Console\Input\InputOption;

class SchemaCreateCommand extends DoctrineSchemaCommand
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
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function fire()
    {
        $tool = $this->getSchemaTool();
        $metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        $sql = $tool->getCreateSchemaSql($metadata);

        if (count($sql) === 0) {
            $this->info('Nothing to create!');

            return;
        }

        $this->info('Creating database schema ...');

        $tool->createSchema($metadata);

        if ($this->option('sql')) {
            $this->info(implode(';'.PHP_EOL, $sql));
        }

        $this->info('Schema created!');
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
