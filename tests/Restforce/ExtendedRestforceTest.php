<?php

namespace SalesforceTest\Job;

use EventFarm\Restforce\Rest\OAuthAccessToken;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Salesforce\Restforce\ExtendedGuzzleRestClient;
use Salesforce\Restforce\ExtendedRestforce;

class ExtendedRestforceTest extends TestCase
{
    /** @var  \Salesforce\Restforce\ExtendedRestforce */
    protected $restforce;

    /** @var \Salesforce\Restforce\ExtendedGuzzleRestClient */
    protected $oAuthRestClient;

    /** @var \GuzzleHttp\Psr7\Response */
    protected $response;

    /**
     * @throws \EventFarm\Restforce\RestforceException
     */
    public function setUp(): void
    {
        parent::setUp();
        $oAuthAccessToken = new OAuthAccessToken('test', 'test', 'test', 'test');
        $this->oAuthRestClient = $this->createMock(ExtendedGuzzleRestClient::class);

        $this->restforce = new ExtendedRestforce('test', 'test', 'test', $oAuthAccessToken, 'test', 'test');
        $this->restforce->setOAuthRestClient($this->oAuthRestClient);

        $this->response = $this->createMock(Response::class);
    }

    public function testCreate()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('postJson')->willReturn($this->response);
        $this->restforce->create('/test', ['test']);
    }

    public function testUpdate()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('patchJson')->willReturn($this->response);
        $this->restforce->update('/test', 'test', ['test']);
    }

    public function testDescribe()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->describe('/test');
    }

    public function testFind()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->find('/test', 'test', ['test', 'test2']);
    }

    public function testlimits()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->limits();
    }

    public function testGetNext()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->getNext('/test');
    }

    public function testQuery()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->query('/test');
    }

    public function testUserInfo()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->userInfo();
    }

    public function testCreateApexObject()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('post')->willReturn($this->response);
        $this->restforce->createApexObject('test', ['test']);
    }

    public function testUpdateApexObject()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('patchJson')->willReturn($this->response);
        $this->restforce->updateApexObject('test', ['test']);
    }

    public function testPpexGet()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->apexGet();
    }

    public function testApexPostJson()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('postJson')->willReturn($this->response);
        $this->restforce->apexPostJson('test', ['test']);
    }

    public function testCreateJob()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('postJson')->willReturn($this->response);
        $this->restforce->createJob('test', ['test']);
    }

    public function testBatchJob()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('putCsv')->willReturn($this->response);
        $this->restforce->batchJob();
    }

    public function testCloseJob()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('patchJson')->willReturn($this->response);
        $this->restforce->closeJob('test', ['tests']);
    }

    public function testGetJob()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->getJob('test');
    }

    public function testFindApexObject()
    {
        $this->oAuthRestClient->expects($this->exactly(1))->method('get')->willReturn($this->response);
        $this->restforce->findApexObject('test', 'test');
    }
}
