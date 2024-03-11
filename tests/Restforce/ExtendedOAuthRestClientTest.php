<?php

namespace SalesforceTest\Job;

use EventFarm\Restforce\Rest\OAuthAccessToken;
use EventFarm\Restforce\Rest\OAuthRestClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Salesforce\Restforce\ExtendedGuzzleRestClient;
use Salesforce\Restforce\ExtendedOAuthRestClient;
use Salesforce\Restforce\ExtendedSalesforceRestClient;

use function GuzzleHttp\Psr7\stream_for;

class ExtendedOAuthRestClientTest extends TestCase
{
    /** @var \Salesforce\Restforce\ExtendedOAuthRestClient */
    protected $oAuthClient;

    /** @var \Salesforce\Restforce\ExtendedSalesforceRestClient */
    protected $salesforceClient;

    /** @var \Salesforce\Restforce\ExtendedGuzzleRestClient */
    protected $extendedGuzzle;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->salesforceClient = $this->createMock(ExtendedSalesforceRestClient::class);
        $this->extendedGuzzle = $this->createMock(ExtendedGuzzleRestClient::class);
        $this->oAuthClient = new ExtendedOAuthRestClient($this->salesforceClient, $this->extendedGuzzle, 'test', 'test');

        $reflection = new ReflectionClass($this->oAuthClient);
        $reflection_property = $reflection->getProperty('oAuthAccessToken');
        $reflection_property->setAccessible(true);
        $oAuthAccessToken = new OAuthAccessToken('test', 'test', 'test', 'test');
        $reflection_property->setValue($this->oAuthClient,$oAuthAccessToken);
    }

    public function testGet()
    {
        $this->salesforceClient->expects($this->exactly(1))->method('setBaseUriForRestClient');
        $this->salesforceClient->expects($this->exactly(1))->method('setResourceOwnerUrl');
        $this->oAuthClient->get('/test');
    }

    public function testPost()
    {
        $this->salesforceClient->expects($this->exactly(1))->method('setBaseUriForRestClient');
        $this->salesforceClient->expects($this->exactly(1))->method('setResourceOwnerUrl');
        $this->oAuthClient->post('/test');
    }

    public function testPostJson()
    {
        $this->salesforceClient->expects($this->exactly(1))->method('setBaseUriForRestClient');
        $this->salesforceClient->expects($this->exactly(1))->method('setResourceOwnerUrl');
        $this->oAuthClient->postJson('/test');
    }

    public function testPatchJson()
    {
        $this->salesforceClient->expects($this->exactly(1))->method('setBaseUriForRestClient');
        $this->salesforceClient->expects($this->exactly(1))->method('setResourceOwnerUrl');
        $this->oAuthClient->patchJson('/test');
    }

    public function testPutCsv()
    {
        $this->salesforceClient->expects($this->exactly(1))->method('setBaseUriForRestClient');
        $this->salesforceClient->expects($this->exactly(1))->method('setResourceOwnerUrl');
        $this->oAuthClient->putCsv('/test');
    }

    public function testExceptionLogging() {
        $reflection = new ReflectionClass($this->oAuthClient);
        $reflection_property = $reflection->getProperty('oAuthAccessToken');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->oAuthClient, null);

        $mockResponse = new Response(400, [], stream_for('{"error": "test"}'));

        $this->extendedGuzzle->method('post')->willReturn($mockResponse);
        $this->expectException(OAuthRestClientException::class);
        $this->expectExceptionMessage('Unable to load access token: {"error": "test"}');
        $this->oAuthClient->get('/test');
    }
}
