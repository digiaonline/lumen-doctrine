<?php

namespace Nord\Lumen\Doctrine\ORM\Test;

use Nord\Lumen\Doctrine\ORM\Configuration\SqliteAdapter;

class SqliteAdapterTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

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

    /**
     * @inheritdoc
     */
    protected function setup()
    {
        $this->sqliteAdapter = new SqliteAdapter();

        $this->fillExpectedConfigurationArray();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->sqliteAdapter = null;
    }

    public function testAssertInstanceOfSqliteAdapter()
    {
        $this->specify('verify sqlliteAdapter is instance of SqliteAdapter', function () {
            verify($this->sqliteAdapter)->isInstanceOf(SqliteAdapter::class);
        });
    }

    public function testMapReturnsCorrectConfiguration()
    {
        $this->fillExpectedConfigurationArray();

        $this->specify('SqliteAdapter::map method returns correct configuration', function () {
            verify($this->sqliteAdapter->map($this->dummyConfiguration))->equals($this->expectedConfiguration);
        });
    }

    public function testMapReturnsCorrectConfigurationWithInMemoryDatabase()
    {
        $this->specify('SqliteAdapter::map method returns correct configuration with in memory database', function () {
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
            'path'     => $this->dummyConfiguration['database'],
        ];
    }

    /**
     * Set the database strategy as memory in the expected configuration
     */
    protected function setInMemoryDatabase()
    {
        $this->dummyConfiguration['database'] = ':memory:';
        unset($this->expectedConfiguration['path']);
        $this->expectedConfiguration['memory'] = true;
    }
}
