<?php

use Nord\Lumen\Doctrine\ORM\Configuration\SqliteAdapter;

class SqliteAdapterTest extends \PHPUnit_Framework_TestCase
{
    use Codeception\Specify;

    /**
     * @var SqliteAdapter
     */
    private $sqliteAdapter;

    /**
     * Expected configuration from the SqlliteAdapter::map.
     *
     * @var array $expectedConfiguration
     */
    private $expectedConfiguration;

    /**
     * Dummy configuration data for the SqlliteAdapter::map.
     *
     * @var array $dummyConfiguration
     */
    private $dummyConfiguration;

    protected function setUp()
    {
        $this->sqliteAdapter = new SqliteAdapter();

        $this->fillExpectedConfigurationArray();
    }

    protected function tearDown()
    {
        $this->sqliteAdapter = null;
    }

    public function testAssertInstanceOfSqliteAdapter()
    {
        $this->specify('verify sqlliteAdapter is instance of SqliteAdapter', function() {
            verify($this->sqliteAdapter)->isInstanceOf(SqliteAdapter::class);
        });
    }

    public function testMapReturnsCorrectConfiguration()
    {
        $this->specify('verify SqliteAdapter::map method returns correct configuration', function() {
            verify($this->sqliteAdapter->map($this->dummyConfiguration))->equals($this->expectedConfiguration);
        });
    }

    /**
     * Responsible for initializing configuration array with dummy data.
     *
     * @return void
     */
    protected function fillDummyConfigurationArray()
    {
        $this->dummyConfiguration = [
            'username' => __DIR__,
            'password' => __DIR__,
            'prefix'   => __DIR__,
            'database' => __DIR__,
        ];
    }

    protected function fillExpectedConfigurationArray()
    {
        $this->fillDummyConfigurationArray();

        $this->expectedConfiguration = [
            'driver'   => 'pdo_sqlite',
            'user'     => $this->dummyConfiguration['username'],
            'password' => $this->dummyConfiguration['password'],
            'prefix'   => $this->dummyConfiguration['prefix'],
        ];

        $this->expectedConfiguration['path'] = $this->dummyConfiguration['database'];

        if (':memory:' === $this->dummyConfiguration['database']) {
            unset($this->expectedConfiguration['path']);

            $this->expectedConfiguration['memory'] = true;
        }
    }
}
