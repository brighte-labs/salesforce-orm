<?php
namespace SalesforceTest\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Salesforce\Cache\CacheEngineFactory;
use Salesforce\Client\Config;
use Salesforce\Client\Connection;
use Salesforce\Client\Result;

class ConnectionTest extends TestCase
{
    /** @var Result */
    protected $result;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var getClientConfig
     */
    private $clientConfig;

    /**
     * @var getEngineConfig
     */
    private $engineConfig;

    /**
     * @var Connection
     */
    private $connection;

    public function setUp(): void
    {
        parent::setUp();
        $logger = $this->createPartialMock(LoggerInterface::class, ['log', 'debug', 'emergency', 'critical', 'error', 'alert', 'warning', 'info', 'notice']);
        $this->logger = $logger;
        $this->engineConfig = $this->getEngineConfig();
        $this->clientConfig = $this->getClientConfig();

        $this->connection = new Connection($this->clientConfig, $this->engineConfig, $this->logger);
    }

    public function testGetAndSetConfig()
    {
        $config = new Config($this->clientConfig);
        $this->connection->setConfig($config);

        $getConfig = $this->connection->getConfig();
        $this->assertSame($getConfig->getClientId(), 'Id');
        $this->assertSame($getConfig->getClientSecret(), 'Secret');
        $this->assertSame($getConfig->getPath(), 'path/path');
        $this->assertSame($getConfig->getUsername(), 'user');
        $this->assertSame($getConfig->getPassword(), 'pass');
        $this->assertSame($getConfig->getApiVersion(), '1.0');
        $this->assertSame($getConfig->getClientSecret(), 'Secret');
        $this->assertSame($getConfig->getApexEndPoint(), 'path/endpoint');
    }

    /**
     * @return array
     */
    public function getEngineConfig()
    {
        return [
            'engine' => 'file',
            'dir' => __DIR__,
            'time' => 36000
        ];
    }

    /**
     * @return array|Config
     * @throws \Salesforce\Client\Exception\ConfigException
     */
    public function getClientConfig()
    {
        $config = [
            'clientId' => 'Id',
            'clientSecret' => 'Secret',
            'path' => 'path/path',
            'username' => 'user',
            'password' => 'pass',
            'apiVersion' => '1.0',
            'apexEndPoint' => 'path/endpoint'
        ];

        return $config;
    }
}
