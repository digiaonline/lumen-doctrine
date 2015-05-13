<?php namespace Nord\Lumen\Doctrine;

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

    const MAPPING_ANNOTATIONS = 'annotations';
    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yaml';


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

        $this->app->singleton(ClassMetadataFactory::class, function ($app) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $app[EntityManagerInterface::class];

            return $entityManager->getMetadataFactory();
        });

        $this->commands([
            'Nord\Lumen\Doctrine\Console\SchemaCreateCommand',
            'Nord\Lumen\Doctrine\Console\SchemaUpdateCommand',
            'Nord\Lumen\Doctrine\Console\SchemaDropCommand',
        ]);
    }


    /**
     * @param Application $app
     *
     * @return \Doctrine\ORM\EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createEntityManager(Application $app)
    {
        $config = $app['config']['doctrine'];

        $connectionConfig = $this->createConnectionConfig($app['config']['database']);

        // TODO: support caching
        $metadataConfig = $this->createMetadataConfiguration(
            array_get($config, 'mapping', self::MAPPING_XML),
            array_get($config, 'paths', [ base_path('app/Entities') ]),
            $app['config']['app.debug'],
            array_get($config, 'proxy.directory'),
            null,
            array_get($config, 'simple_annotations', false)
        );

        $this->configureMetadata($metadataConfig, $config);

        $eventManager = new EventManager();

        $entityManager = EntityManager::create($connectionConfig, $metadataConfig, $eventManager);

        if (isset( $config['types'] )) {
            $this->registerTypes($config['types'], $entityManager);
        }

        return $entityManager;
    }


    /**
     * @param array $config
     *
     * @return array
     */
    protected function createConnectionConfig(array $config)
    {
        $default    = $config['default'];
        $connection = $config['connections'][$default];

        return $this->normalizeConnectionConfig($connection);
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

        if ( ! isset( $driverMap[$config['driver']] )) {
            throw new Exception("Driver '{$config['driver']}' is not supported.");
        }

        return [
            'driver'   => $driverMap[$config['driver']],
            'host'     => $config['host'],
            'dbname'   => $config['database'],
            'user'     => $config['username'],
            'password' => $config['password'],
            'charset'  => $config['charset'],
            'prefix'   => array_get($config, 'prefix')
        ];
    }


    /**
     * @param $type
     * @param $paths
     * @param $isDevMode
     * @param $proxyDir
     * @param $cache
     * @param $useSimpleAnnotationReader
     *
     * @return \Doctrine\ORM\Configuration
     * @throws \Exception
     */
    protected function createMetadataConfiguration(
        $type,
        $paths,
        $isDevMode,
        $proxyDir,
        $cache,
        $useSimpleAnnotationReader
    ) {
        switch ($type) {
            case self::MAPPING_ANNOTATIONS:
                return Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache,
                    $useSimpleAnnotationReader);
            case self::MAPPING_XML:
                return Setup::createXMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            case self::MAPPING_YAML:
                return Setup::createYAMLMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache);
            default:
                throw new Exception("Metadata type '$type' is not supported.");
        }
    }


    /**
     * @param \Doctrine\ORM\Configuration $metadataConfig
     * @param array                       $config
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function configureMetadata(
        Configuration $metadataConfig,
        array $config
    ) {
        if (isset( $config['proxy'] ) && isset( $config['proxy']['auto_generate'] )) {
            $metadataConfig->setAutoGenerateProxyClasses($config['proxy']['auto_generate']);
        }
        if (isset( $config['proxy'] ) && isset( $config['proxy']['namespace'] )) {
            $metadataConfig->setProxyNamespace($config['proxy']['namespace']);
        }
        if (isset( $config['repository'] )) {
            $metadataConfig->setDefaultRepositoryClassName($config['repository']);
        }
        if (isset( $config['logger'] )) {
            $metadataConfig->setSQLLogger($config['logger']);
        }
    }


    /**
     * @param array                       $types
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function registerTypes(array $types, EntityManager $entityManager)
    {
        $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();

        foreach ($types as $name => $className) {
            Type::addType($name, $className);
            $databasePlatform->registerDoctrineTypeMapping('db_' . $name, $name);
        }
    }
}
