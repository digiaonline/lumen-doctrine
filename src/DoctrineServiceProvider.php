<?php namespace Nord\Lumen\Doctrine\ORM;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\SimpleYamlDriver;
use Exception;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Nord\Lumen\Doctrine\ORM\Configuration\ConnectionConfiguration;
use Nord\Lumen\Doctrine\ORM\Configuration\SqlAdapter;
use Nord\Lumen\Doctrine\ORM\Configuration\SqliteAdapter;
use Nord\Lumen\Doctrine\ORM\Contracts\ConfigurationAdapter;

class DoctrineServiceProvider extends ServiceProvider
{

    const METADATA_ANNOTATIONS = 'annotations';
    const METADATA_XML         = 'xml';
    const METADATA_YAML        = 'yaml';
    const METADATA_SIMPLEYAML  = 'simpleyaml';

    const DRIVER_MYSQL  = 'mysql';
    const DRIVER_PGSQL  = 'pgsql';
    const DRIVER_SQLSRV = 'sqlsrv';
    const DRIVER_SQLITE = 'sqlite';


    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerContainerBindings($this->app, $this->app['config']);
        $this->registerFacades();
        $this->registerCommands();
    }


    /**
     * Registers container bindings.
     *
     * @param Container        $container
     * @param ConfigRepository $config
     */
    protected function registerContainerBindings(Container $container, ConfigRepository $config)
    {
        $container->singleton('Doctrine\ORM\EntityManager', function () use ($config) {
            return $this->createEntityManager($config);
        });

        $container->alias('Doctrine\ORM\EntityManager', 'Doctrine\ORM\EntityManagerInterface');
    }


    /**
     * Registers facades.
     */
    protected function registerFacades()
    {
        class_alias('Nord\Lumen\Doctrine\ORM\Facades\EntityManager', 'EntityManager');
    }


    /**
     * Registers console commands.
     */
    protected function registerCommands()
    {
        $this->commands([
            'Nord\Lumen\Doctrine\ORM\Console\GenerateProxiesCommand',
            'Nord\Lumen\Doctrine\ORM\Console\SchemaCreateCommand',
            'Nord\Lumen\Doctrine\ORM\Console\SchemaDropCommand',
            'Nord\Lumen\Doctrine\ORM\Console\SchemaUpdateCommand',
            'Nord\Lumen\Doctrine\ORM\Console\FixturesLoadCommand',
        ]);
    }


    /**
     * Creates the Doctrine entity manager instance.
     *
     * @param ConfigRepository $config
     *
     * @return EntityManager
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createEntityManager(ConfigRepository $config)
    {
        if (!isset($config['doctrine'])) {
            throw new Exception('Doctrine configuration not registered.');
        }

        if (!isset($config['database'])) {
            throw new Exception('Database configuration not registered.');
        }

        $doctrineConfig = $config['doctrine'];
        $databaseConfig = $config['database'];


        $connectionConfig = $this->createConnectionConfig($doctrineConfig, $databaseConfig);

        $type              = array_get($doctrineConfig, 'mapping', self::METADATA_ANNOTATIONS);
        $paths             = array_get($doctrineConfig, 'paths', [base_path('app/Entities')]);
        $debug             = $config['app.debug'];
        $proxyDir          = array_get($doctrineConfig, 'proxy.directory');
        $simpleAnnotations = array_get($doctrineConfig, 'simple_annotations', false);
        $yamlpaths         = array_get($doctrineConfig, 'yamlpaths', false);

        $metadataConfiguration = $this->createMetadataConfiguration($type, $paths, $debug, $proxyDir, null,
            $simpleAnnotations, $yamlpaths);

        $this->configureMetadataConfiguration($metadataConfiguration, $doctrineConfig);

        $eventManager = new EventManager;

        $this->configureEventManager($doctrineConfig, $eventManager);

        $entityManager = EntityManager::create($connectionConfig, $metadataConfiguration, $eventManager);

        $this->configureEntityManager($doctrineConfig, $entityManager);

        return $entityManager;
    }


    /**
     * Creates the Doctrine connection configuration.
     *
     * @param array $doctrineConfig
     * @param array $databaseConfig
     *
     * @return array
     * @throws Exception
     */
    protected function createConnectionConfig(array $doctrineConfig, array $databaseConfig)
    {
        $connectionName   = array_get($doctrineConfig, 'connection', $databaseConfig['default']);
        $connectionConfig = array_get($databaseConfig['connections'], $connectionName);

        if ($connectionConfig === null) {
            throw new Exception("Configuration for connection '$connectionName' not found.");
        }

        return $this->normalizeConnectionConfig($connectionConfig);
    }


    /**
     * Normalizes the connection config to a format Doctrine can use.
     *
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    protected function normalizeConnectionConfig(array $config)
    {
        $adapter = $this->createConfigurationAdapter($config['driver']);

        $configuration = new ConnectionConfiguration($adapter);

        return $configuration->map($config);
    }


    /**
     * @param string $driver
     *
     * @return ConfigurationAdapter
     * @throws Exception
     */
    protected function createConfigurationAdapter($driver)
    {
        switch ($driver) {
            case self::DRIVER_MYSQL:
            case self::DRIVER_PGSQL:
            case self::DRIVER_SQLSRV:
                return new SqlAdapter();
            case self::DRIVER_SQLITE:
                return new SqliteAdapter();
            default:
                throw new Exception("Driver '{$driver}' is not supported.");
        }
    }


    /**
     * Creates the metadata configuration instance.
     *
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
        $useSimpleAnnotationReader = true,
        $yamlpaths = false
    ) {
        switch ($type) {
            case self::METADATA_SIMPLEYAML:

                if(is_array($yamlpaths)) {
                
                    $config = Setup::createConfiguration($isDevMode, $proxyDir, $cache);
                    $config->setMetadataDriverImpl(new \Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver($yamlpaths));

                    return $config;
                }

                throw new Exception("'yamlpaths' configuration is missing.");

            case self::METADATA_ANNOTATIONS:
                return Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
            
            case self::METADATA_XML:
                return Setup::createXMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            
            case self::METADATA_YAML:
                return Setup::createYAMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            
            default:
                throw new Exception("Metadata type '$type' is not supported.");
        }
    }


    /**
     * Configures the metadata configuration instance.
     *
     * @param Configuration $configuration
     * @param array         $doctrineConfig
     *
     * @throws ORMException
     */
    protected function configureMetadataConfiguration(
        Configuration $configuration,
        array $doctrineConfig
    ) {
        if (isset($doctrineConfig['filters'])) {
            foreach ($doctrineConfig['filters'] as $name => $filter) {
                $configuration->addFilter($name, $filter['class']);
            }
        }
        if (isset($doctrineConfig['logger'])) {
            $configuration->setSQLLogger($doctrineConfig['logger']);
        }
        if (isset($doctrineConfig['proxy']) && isset($doctrineConfig['proxy']['auto_generate'])) {
            $configuration->setAutoGenerateProxyClasses($doctrineConfig['proxy']['auto_generate']);
        }
        if (isset($doctrineConfig['proxy']) && isset($doctrineConfig['proxy']['namespace'])) {
            $configuration->setProxyNamespace($doctrineConfig['proxy']['namespace']);
        }
        if (isset($doctrineConfig['repository'])) {
            $configuration->setDefaultRepositoryClassName($doctrineConfig['repository']);
        }

        $namingStrategy = array_get($doctrineConfig, 'naming_strategy', 'Nord\Lumen\Doctrine\ORM\NamingStrategy');
        $configuration->setNamingStrategy(new $namingStrategy);

        // set file extension
        if(isset($doctrineConfig['mappingextension'])) {
            $driver = $configuration->getMetadataDriverImpl()->getLocator();
            $driver->setFileExtension($doctrineConfig['mappingextension']);
        }
    }


    /**
     * Configures the Doctrine event manager instance.
     *
     * @param array        $doctrineConfig
     * @param EventManager $eventManager
     */
    protected function configureEventManager(array $doctrineConfig, EventManager $eventManager)
    {
        if (isset($doctrineConfig['event_listeners'])) {
            foreach ($doctrineConfig['event_listeners'] as $name => $listener) {
                $eventManager->addEventListener($listener['events'], new $listener['class']);
            }
        }
    }


    /**
     * Configures the Doctrine entity manager instance.
     *
     * @param array         $doctrineConfig
     * @param EntityManager $entityManager
     */
    protected function configureEntityManager(array $doctrineConfig, EntityManager $entityManager)
    {
        if (isset($doctrineConfig['filters'])) {
            foreach ($doctrineConfig['filters'] as $name => $filter) {
                if (!array_get($filter, 'enabled', false)) {
                    continue;
                }

                $entityManager->getFilters()->enable($name);
            }
        }

        if (isset($doctrineConfig['types'])) {
            $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();

            foreach ($doctrineConfig['types'] as $name => $className) {
                Type::addType($name, $className);
                $databasePlatform->registerDoctrineTypeMapping($name, $name);
            }
        }
    }
}
