<?php

use Nord\Lumen\Doctrine\ORM\Configuration\SqlAdapter;

class SqlAdapterTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    /**
     * @var SqlAdapter
     */
    private $sqlAdapter;

    /**
     * @var array available database drivers
     */
    private static $availableDrivers = [
        'mysql' => 'pdo_mysql',
        'pgsql'  => 'pdo_pgsql',
        'sqlsrv' => 'pdo_sqlsrv',
    ];

    /**
     * Expected configuration from the SqlAdapter::map.
     *
     * @var array $expectedConfiguration
     */
    private $expectedConfiguration;

    /**
     * Dummy configuration data for the SqlAdapter::map.
     *
     * @var array $dummyConfiguration
     */
    private $dummyConfiguration;

    protected function setUp()
    {
        $this->sqlAdapter = new SqlAdapter();

        $this->fillExpectedConfigurationArray();
    }

    protected function tearDown()
    {
        $this->sqlAdapter = null;
    }

    public function testAssertInstanceOfSqlAdapter()
    {
        $this->specify('verify SqlAdapter is instance of \SqlAdapter', function() {
            verify($this->sqlAdapter)->isInstanceOf(SqlAdapter::class);
        });
    }

    public function testMapReturnsCorrectConfiguration()
    {
        $this->specify('verify SqlAdapter::map mathod returns correct configuration', function() {
            verify($this->sqlAdapter->map($this->dummyConfiguration))->equals($this->expectedConfiguration);
        });
    }

    /**
     * Responsible for filling expected configuration array.
     *
     * @return void
     */
    protected function fillExpectedConfigurationArray()
    {
        $this->fillDummyConfigurationArray();

        $this->expectedConfiguration = [
            'driver'   => self::$availableDrivers[$this->dummyConfiguration['driver']],
            'host'     => $this->dummyConfiguration['host'],
            'port'     => $this->dummyConfiguration['port'],
            'dbname'   => $this->dummyConfiguration['database'],
            'user'     => $this->dummyConfiguration['username'],
            'password' => $this->dummyConfiguration['password'],
            'charset'  => $this->dummyConfiguration['charset'],
            'prefix'   => $this->dummyConfiguration['prefix'],
        ];
    }

    /**
     * Responsible for initializing configuration array with dummy data.
     *
     * @return void
     */
    protected function fillDummyConfigurationArray()
    {
        $this->dummyConfiguration = [
            'driver'   => 'mysql',
            'host'     => __DIR__,
            'port'     => __LINE__,
            'database' => __DIR__,
            'username' => __DIR__,
            'password' => __DIR__,
            'charset'  => 'utf8',
            'prefix'   => 'prefix',
        ];
    }
}
