<?php

namespace SalesforceTest\Job;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Salesforce\Restforce\ExtendedGuzzleRestClient;

class ExtendedGuzzleRestClientTest extends TestCase
{
    /** @var \Salesforce\Restforce\ExtendedGuzzleRestClient */
    protected $guzzleClient;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \GuzzleHttp\Psr7\Response */
    protected $response;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->guzzleClient = new ExtendedGuzzleRestClient('test');
        $this->client = $this->createMock(Client::class);

        $reflection = new ReflectionClass($this->guzzleClient);
        $reflection_property = $reflection->getProperty('client');
        $reflection_property->setAccessible(true);

        $reflection_property->setValue($this->guzzleClient, $this->client);

        $this->response = $this->createMock(Response::class);
    }

    public function testGet()
    {
        $this->client->expects($this->exactly(1))->method('request')->willReturn($this->response);
        $this->guzzleClient->get('/test');
    }

    public function testPost()
    {
        $this->client->expects($this->exactly(1))->method('request')->willReturn($this->response);
        $this->guzzleClient->post('/test');
    }

    public function testPostJson()
    {
        $this->client->expects($this->exactly(1))->method('request')->willReturn($this->response);
        $this->guzzleClient->postJson('/test');
    }

    public function testPatchJson()
    {
        $this->client->expects($this->exactly(1))->method('request')->willReturn($this->response);
        $this->guzzleClient->patchJson('/test');
    }

    public function testPutCsv()
    {
        $this->client->expects($this->exactly(1))->method('request')->willReturn($this->response);
        $this->guzzleClient->putCsv('/test');
    }
}
