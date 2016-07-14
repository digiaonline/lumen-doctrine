<?php

namespace Nord\Lumen\Doctrine\ORM\Console;

use Symfony\Component\Console\Input\InputOption;

class SchemaDropCommand extends DoctrineSchemaCommand
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
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function fire()
    {
        $tool = $this->getSchemaTool();
        $metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        $sql = $tool->getDropSchemaSQL($metadata);

        if (count($sql) === 0) {
            $this->info('Nothing to drop!');

            return;
        }

        $this->info('Dropping database schema ...');

        $tool->dropSchema($metadata);

        if ($this->option('sql')) {
            $this->info(implode(';'.PHP_EOL, $sql));
        }

        $this->info('Schema dropped!');
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
