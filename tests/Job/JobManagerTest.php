<?php

namespace SalesforceTest\Job;

use PHPUnit\Framework\TestCase;
use Salesforce\Client\Client;
use Salesforce\Client\Connection;
use Salesforce\Entity\Account;
use Salesforce\Job\Bulk\InsertJob;
use Salesforce\Job\Job;
use Salesforce\Job\JobManager;
use Salesforce\ORM\Mapper;

class JobManagerTest extends TestCase
{
    /** @var $jobManager JobManager */
    protected $jobManager;

    /** @var \Salesforce\Client\Connection */
    protected $connection;

    /** @var \Salesforce\ORM\Mapper */
    protected $mapper;

    /** @var \Salesforce\Event\EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var \Salesforce\ORM\EntityFactory */
    protected $entityFactory;

    /** @var \Salesforce\Client\Client */
    protected $client;

    /** @var \Salesforce\Job\Job */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->createMock(Connection::class);
        $this->client = $this->createMock(Client::class);
        $this->jobManager = new JobManager($this->connection);
        $this->job = new InsertJob();
        $this->job->setCsvData([['Name'], ['NAME 1']]);
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testProperties()
    {
        $mapper = new Mapper();

        // set properties
        $jobManager = $this->jobManager;
        $jobManager->setMapper($mapper);
        $jobManager->setEventDispatcher(null);
        $jobManager->setConnection($this->connection);

        // get properties
        $this->assertEquals($jobManager->getMapper(), $mapper);
        $this->assertEquals($jobManager->getEventDispatcher(), null);
        $this->assertEquals($jobManager->getConnection(), $this->connection);
    }

    /**
     * @throws \Salesforce\Client\Exception\ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     * @throws \Salesforce\ORM\Exception\MapperException
     */
    public function testRegisterJob()
    {
        $this->connection->expects($this->exactly(1))->method('getClient')->willReturn($this->client);
        $this->client->expects($this->exactly(1))->method('createJob')->willReturn([
            Job::JOB_FIELD_ID => 'id',
            Job::JOB_FIELD_STATE => 'Active'
        ]);
        $this->jobManager->registerJob(Account::class, $this->job);
    }

    /**
     * @throws \League\Csv\CannotInsertRecord
     * @throws \Salesforce\Client\Exception\ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     * @throws \Salesforce\Job\Exception\JobException
     * @throws \TypeError
     */
    public function testProcessBatchJob()
    {
        $this->job->setId('test');
        $this->connection->expects($this->exactly(1))->method('getClient')->willReturn($this->client);
        $this->client->expects($this->exactly(1))->method('batchJob')->willReturn(True);
        $this->jobManager->processBatchJob($this->job);
    }

    /**
     * @throws \Salesforce\Client\Exception\ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     * @throws \Salesforce\Job\Exception\JobException
     */
    public function testCloseJob()
    {
        $this->job->setId('test');
        $this->connection->expects($this->exactly(1))->method('getClient')->willReturn($this->client);
        $this->client->expects($this->exactly(1))->method('closeJob')->willReturn([Job::JOB_FIELD_STATE => Job::STATE_UPLOAD_COMPLETE]);
        $this->jobManager->closeJob($this->job);
    }

    /**
     * @throws \Salesforce\Client\Exception\ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testGetJobInfo()
    {
        $this->job->setId('test');
        $this->connection->expects($this->exactly(1))->method('getClient')->willReturn($this->client);
        $this->client->expects($this->exactly(1))->method('getJob')->willReturn([Job::JOB_FIELD_STATE => Job::STATE_UPLOAD_COMPLETE]);
        $this->jobManager->getJobInfo($this->job);
    }

    /**
     * @throws \Salesforce\Client\Exception\ClientException
     * @throws \Salesforce\Client\Exception\ResultException
     */
    public function testGetJobResult()
    {
        $this->job->setId('test');
        $this->connection->expects($this->exactly(3))->method('getClient')->willReturn($this->client);
        $this->client->expects($this->exactly(3))->method('getJob')->willReturn('"test","test1"');
        $jobResult = $this->jobManager->getJobResult($this->job);
        $this->assertEquals($jobResult->getFailedResult(), [['test', 'test1']]);
        $this->assertEquals($jobResult->getSuccessfulResult(), [['test', 'test1']]);
        $this->assertEquals($jobResult->getSuccessfulResult(), [['test', 'test1']]);
    }
}
