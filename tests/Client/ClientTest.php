<?php
namespace SalesforceTest\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Salesforce\Cache\CacheEngineFactory;
use Salesforce\Client\Client;
use Salesforce\Client\Config;
use Salesforce\Client\Exception\ClientException;
use Salesforce\Client\Result;
use Salesforce\Restforce\ExtendedRestforce;

class ClientTest extends TestCase
{
    /** @var Result */
    protected $result;

    /**
     * @var Client
     */
    private $client;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $restforce;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CacheEngineFactory
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface|null $logger
     * @throws \EventFarm\Restforce\RestforceException
     * @throws \Salesforce\Cache\Exception\CacheException
     * @throws \Salesforce\Client\Exception\ConfigException
     */
    public function setUp(LoggerInterface $logger = null): void
    {
        parent::setUp();
        $this->result = new Result();
        $this->restforce = $this->createMock(ExtendedRestforce::class);
        $this->config = $this->getClientConfig();
        $this->cache = CacheEngineFactory::createCacheEngine($this->getEngineConfig());
        $logger = $this->createPartialMock(LoggerInterface::class, ['log', 'debug', 'emergency', 'critical', 'error', 'alert', 'warning', 'info', 'notice']);
        $this->logger = $logger;

        $this->client = new Client($this->getClientConfig(), $this->cache, $this->logger);

        //test getter and setters
        $this->client->setRestforce($this->restforce);
        $this->restforce = $this->client->getRestforce();
        $this->client->setLogger($this->logger);
        $this->logger = $this->client->getLogger();
        $this->client->setCache($this->cache);
        $this->cache = $this->client->getCache();
        $this->config = $this->client->setConfig($this->config);
        $this->config = $this->client->getConfig();
    }

    /**
     * @throws ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testCreateObject()
    {
        $object = '';
        $data = ['data'];

        try {
            $this->client->createObject($object, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_TYPE_MISSING);
        }

        $object = 'something';
        $result =$this->client->createObject($object, $data);
        $this->assertFalse($result);

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('create')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);
        try {
            $this->client->createObject($object, $data);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_FAILED_TO_CREATE_OBJECT);
        }
    }

    /**
     * @throws ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testCreateJob()
    {
        $uri = 'test.com';
        $object = 'test';
        $action = 'action';
        $additionalData = ['test' => 'test'];

        try {
            $this->client->createJob();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        try {
            $this->client->createJob($uri);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_TYPE_MISSING);
        }

        try {
            $this->client->createJob($uri, $object);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_ACTION_MISSING);
        }

        $this->client->getLogger()->expects($this->exactly(1))->method('debug');

        $result = $this->client->createJob($uri, $object, $action, $additionalData);
        $this->assertFalse($result);
    }

    /**
     * @throws ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testBatchJob()
    {
        $uri = 'test.com';
        $csvData = 'test,test1';

        try {
            $this->client->batchJob();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        $this->client->getLogger()->expects($this->exactly(1))->method('debug');

        $result = $this->client->batchJob($uri, $csvData);
        $this->assertFalse($result);
    }

    /**
     * @throws ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testCloseJob()
    {
        $uri = 'test.com';

        try {
            $this->client->closeJob();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        $this->client->getLogger()->expects($this->exactly(1))->method('debug');

        $result = $this->client->closeJob($uri);
        $this->assertFalse($result);
    }

    /**
     * @throws ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testGetJob()
    {
        $uri = 'test.com';

        try {
            $this->client->getJob();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        $result = $this->client->getJob($uri);
        $this->assertFalse($result);
    }

    /**
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testUpdateObject()
    {
        $object = '';
        $sobject = '';
        $data = ['data'];

        // Test: Object = ''
        try {
            $this->client->updateObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_TYPE_MISSING);
        }

        // Test: SObjectId = ''
        $object = 'object';
        $sobject = '';
        try {
            $this->client->updateObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // Test: Valid inputs
        $object = 'object';
        $sobject = 'sobject';
        try {
            $this->client->updateObject($object, $sobject,$data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('update')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);


        try {
            $this->client->updateObject($object, $sobject, $data);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_FAILED_TO_UPDATE_OBJECT);
        }
    }

    /**
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testQuery()
    {
        $query = '';

        // Test: query = ''
        try {
            $this->client->query($query);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_QUERY_MISSING);
        }

        // Test: Valid inputs
        $query = 'Query';
        try {
            $this->client->query($query);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // clear the cache to null
        $this->client->setCache();
        try {
            $this->client->query($query);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('query')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);
        $this->client->setLogger($this->logger);

        try {
            $this->client->query($query);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_FAILED_TO_FIND_OBJECT);
        }
    }

    /**
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testApexPostJson()
    {
        $uri = '';
        $data = ['data'];

        // Test: $uri = ''
        try {
            $this->client->apexPostJson($uri, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        // Test: Valid inputs
        $uri = 'Uri';
        try {
            $this->client->apexPostJson($uri, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('apexPostJson')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);
        $this->client->setLogger($this->logger);

        try {
            $this->client->apexPostJson($uri, $data);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_FAILED);
        }
    }

    /**
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testApexGet()
    {
        $uri = '';

        // Test: $uri = ''
        try {
            $this->client->apexGet($uri);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_URI_MISSING);
        }

        // Test: Valid inputs
        $uri = 'Uri';
        try {
            $this->client->apexGet($uri);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        try {
            $this->client->apexGet($uri);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('apexGet')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);
        $this->client->setLogger($this->logger);

        try {
            $this->client->apexGet($uri);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_APEX_API_FAILED);
        }
    }

    /**
     * @param LoggerInterface|null $logger
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testFindObject(LoggerInterface $logger = null)
    {
        $object = '';
        $sobject = '';
        $data = ['data'];

        // Test: Object = ''
        try {
            $this->client->findObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_TYPE_MISSING);
        }

        // Test: SObjectId = ''
        $object = 'object';
        $sobject = '';
        try {
            $this->client->findObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // Test: Valid inputs
        $object = 'object';
        $sobject = 'sobject';
        try {
            $this->client->findObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // clear the cache to null
        $this->client->setCache();
        try {
            $this->client->findObject($object, $sobject, $data);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_OBJECT_ID_MISSING);
        }

        // force an exception
        $exception = new ClientException();
        $this->restforce->method('find')->willThrowException($exception);
        $this->client->setRestforce($this->restforce);
        $this->client->setLogger($this->logger);

        try {
            $this->client->findObject($object, $sobject, $data);
        } catch (ClientException $e) {
            $this->assertEquals($e->getMessage(),ClientException::MSG_FAILED_TO_FIND_OBJECT);
        }
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
           'clientId' => '',
               'clientSecret' => '',
               'path' => '',
               'username' => '',
               'password' => '',
               'apiVersion' => '',
               'apexEndPoint' => ''
           ];
        $config = new Config($config);

        $config->setApexEndPoint('endpoint/test');
        $config->setApiVersion('1.0');
        $config->setClientId('123');
        $config->setClientSecret('secret');
        $config->setPassword('password');
        $config->setUsername('test');
        $config->setPath('/path');

        return $config;
    }
}
