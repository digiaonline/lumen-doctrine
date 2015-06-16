<?php namespace Nord\Lumen\Doctrine;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class DoctrineServiceProvider extends ServiceProvider
{

    const METADATA_ANNOTATIONS = 'annotations';
    const METADATA_XML = 'xml';
    const METADATA_YAML = 'yaml';


    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(EntityManager::class, function ($app) {
            return $this->createEntityManager($app);
        });

        $this->app->alias(EntityManager::class, EntityManagerInterface::class);

        class_alias(EntityManagerFacade::class, 'EntityManager');

        $this->commands([
            'Nord\Lumen\Doctrine\Console\GenerateProxiesCommand',
            'Nord\Lumen\Doctrine\Console\SchemaCreateCommand',
            'Nord\Lumen\Doctrine\Console\SchemaDropCommand',
            'Nord\Lumen\Doctrine\Console\SchemaUpdateCommand',
        ]);
    }


    /**
     * @param Application $app
     *
     * @return EntityManager
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createEntityManager(Application $app)
    {
        if (!isset($app['config']['doctrine'])) {
            throw new Exception('Doctrine configuration not registered.');
        }

        $config = $app['config']['doctrine'];

        if (!isset($app['config']['database'])) {
            throw new Exception('Database configuration not registered.');
        }

        $connectionConfig = $this->createConnectionConfig($config, $app['config']['database']);

        $metadataConfiguration = $this->createMetadataConfiguration(
            array_get($config, 'mapping', self::METADATA_ANNOTATIONS),
            array_get($config, 'paths', [base_path('app/Entities')]),
            $app['config']['app.debug'],
            array_get($config, 'proxy.directory'),
            null,
            array_get($config, 'simple_annotations', false)
        );

        $this->configureMetadataConfiguration($metadataConfiguration, $config);

        $eventManager = new EventManager();

        $this->configureEventManager($config, $eventManager);

        $entityManager = EntityManager::create($connectionConfig, $metadataConfiguration, $eventManager);

        $this->configureEntityManager($config, $entityManager);

        return $entityManager;
    }


    /**
     * @param array $config
     * @param array $databaseConfig
     *
     * @return array
     * @throws Exception
     */
    protected function createConnectionConfig(array $config, array $databaseConfig)
    {
        $connection       = array_get($config, 'connection', $databaseConfig['default']);
        $connectionConfig = array_get($databaseConfig['connections'], $connection);

        if ($connectionConfig === null) {
            throw new Exception("Configuration for connection '$connection' not found.");
        }

        return $this->normalizeConnectionConfig($connectionConfig);
    }


    /**
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    protected function normalizeConnectionConfig(array $config)
    {
        $driverMap = [
            'mysql'  => 'pdo_mysql',
            'pgsql'  => 'pdo_pgsql',
            'sqlsrv' => 'pdo_sqlsrv',
        ];

        if (!isset($driverMap[$config['driver']])) {
            throw new Exception("Driver '{$config['driver']}' is not supported.");
        }

        return [
            'driver'   => $driverMap[$config['driver']],
            'host'     => $config['host'],
            'dbname'   => $config['database'],
            'user'     => $config['username'],
            'password' => $config['password'],
            'charset'  => $config['charset'],
            'prefix'   => array_get($config, 'prefix'),
        ];
    }


    /**
     * @param string $type
     * @param array  $paths
     * @param bool   $isDevMode
     * @param string $proxyDir
     * @param Cache  $cache
     * @param bool   $useSimpleAnnotationReader
     *
     * @return Configuration
     * @throws \Exception
     */
    protected function createMetadataConfiguration(
        $type,
        $paths,
        $isDevMode,
        $proxyDir,
        $cache,
        $useSimpleAnnotationReader = true
    ) {
        switch ($type) {
            case self::METADATA_ANNOTATIONS:
                return Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache,
                    $useSimpleAnnotationReader);
            case self::METADATA_XML:
                return Setup::createXMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            case self::METADATA_YAML:
                return Setup::createYAMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            default:
                throw new Exception("Metadata type '$type' is not supported.");
        }
    }


    /**
     * @param \Doctrine\ORM\Configuration $configuration
     * @param array                       $config
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function configureMetadataConfiguration(
        Configuration $configuration,
        array $config
    ) {
        if (isset($config['filters'])) {
            foreach ($config['filters'] as $name => $filter) {
                $configuration->addFilter($name, $filter['class']);
            }
        }
        if (isset($config['logger'])) {
            $configuration->setSQLLogger($config['logger']);
        }
        if (isset($config['proxy']) && isset($config['proxy']['auto_generate'])) {
            $configuration->setAutoGenerateProxyClasses($config['proxy']['auto_generate']);
        }
        if (isset($config['proxy']) && isset($config['proxy']['namespace'])) {
            $configuration->setProxyNamespace($config['proxy']['namespace']);
        }
        if (isset($config['repository'])) {
            $configuration->setDefaultRepositoryClassName($config['repository']);
        }
        $namingStrategy = array_get($config, 'naming_strategy', NamingStrategy::class);
        $configuration->setNamingStrategy(new $namingStrategy);
    }


    /**
     * @param array        $config
     * @param EventManager $eventManager
     */
    protected function configureEventManager(array $config, EventManager $eventManager)
    {
        if (isset($config['event_listeners'])) {
            foreach ($config['event_listeners'] as $name => $listener) {
                $eventManager->addEventListener($listener['events'], new $listener['class']);
            }
        }
    }


    /**
     * @param array         $config
     * @param EntityManager $entityManager
     */
    protected function configureEntityManager(array $config, EntityManager $entityManager)
    {
        if (isset($config['filters'])) {
            foreach ($config['filters'] as $name => $filter) {
                if (!array_get($filter, 'enabled', false)) {
                    continue;
                }

                $entityManager->getFilters()->enable($name);
            }
        }

        if (isset($config['types'])) {
            $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();

            foreach ($config['types'] as $name => $className) {
                Type::addType($name, $className);
                $databasePlatform->registerDoctrineTypeMapping('db_' . $name, $name);
            }
        }
    }
}
