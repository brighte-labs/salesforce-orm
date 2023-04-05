<?php

namespace SalesforceTest\Job;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Salesforce\Restforce\ExtendedGuzzleRestClient;
use Salesforce\Restforce\ExtendedSalesforceRestClient;

class ExtendedSalesforceRestClientTest extends TestCase
{
    /** @var \Salesforce\Restforce\ExtendedSalesforceRestClient */
    protected $salesforceClient;

    /** @var \Salesforce\Restforce\ExtendedGuzzleRestClient */
    protected $guzzleClient;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \GuzzleHttp\Psr7\Response */
    protected $response;

    public function setUp(): void
    {
        parent::setUp();
        $this->guzzleClient = $this->createMock(ExtendedGuzzleRestClient::class);
        $this->salesforceClient = new ExtendedSalesforceRestClient($this->guzzleClient, 'test');

        $this->response = $this->createMock(Response::class);
    }

    public function testGet()
    {
        $this->guzzleClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->salesforceClient->get('/test');
    }

    public function testPost()
    {
        $this->guzzleClient->expects($this->exactly(1))->method('post')->willReturn($this->response);
        $this->salesforceClient->post('/test');
    }

    public function testPostJson()
    {
        $this->guzzleClient->expects($this->exactly(1))->method('postJson')->willReturn($this->response);
        $this->salesforceClient->postJson('/test');
    }

    public function testPatchJson()
    {
        $this->guzzleClient->expects($this->exactly(1))->method('patchJson')->willReturn($this->response);
        $this->salesforceClient->patchJson('/test');
    }

    public function testPutCsv()
    {
        $this->guzzleClient->expects($this->exactly(1))->method('putCsv')->willReturn($this->response);
        $this->salesforceClient->putCsv('/test');
    }
}
