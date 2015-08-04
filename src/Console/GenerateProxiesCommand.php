<?php namespace Nord\Lumen\Doctrine\ORM\Console;

class GenerateProxiesCommand extends DoctrineCommand
{

    /**
     * @var string
     */
    protected $name = 'doctrine:generate:proxies';

    /**
     * @var string
     */
    protected $description = 'Generates proxies for entities.';


    /**
     * @inheritdoc
     */
    public function fire()
    {
        $metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        if (empty( $metadata )) {
            $this->error('No metadata found.');
            exit;
        }

        $directory = array_get($this->laravel['config'], 'doctrine.proxy.directory');

        if ( ! $directory) {
            $this->error('Proxy directory must be set.');
            exit;
        }

        $this->info('Generating proxies ...');

        foreach ($metadata as $item) {
            $this->line($item->name);
        }

        $this->getEntityManager()->getProxyFactory()->generateProxyClasses($metadata, $directory);

        $this->info('Proxies created!');
    }
}
