<?php

namespace SalesforceTest\Job;

use PHPUnit\Framework\TestCase;
use Salesforce\Job\Job;
use Salesforce\ORM\Entity;
use Salesforce\ORM\EntityFactory;
use Salesforce\ORM\Mapper;

class JobTest extends TestCase
{
    /** @var $job Job */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new Job();
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testProperties()
    {
        $entity = new Entity();
        $entityFactory = new EntityFactory();
        $mapper = new Mapper();

        // set properties
        $job = $this->job;
        $job->setId('121');
        $job->setOperation('Name');
        $job->setObject('Owner123');
        $job->setEntity($entity);
        $job->setState('Sydney');
        $job->setMapper($mapper);
        $job->setEntityFactory($entityFactory);
        $job->setRequestBody([]);
        $job->setBaseUrl('Street');
        $job->setSuccessResultUrl('Street');
        $job->setFailedResultUrl('Street');
        $job->setUnprocessedResultUrl('Street');

        // get properties
        $this->assertEquals($job->getId(), '121');
        $this->assertEquals($job->getOperation(), 'Name');
        $this->assertEquals($job->getObject(), 'Owner123');
        $this->assertEquals($job->getEntity(), $entity);
        $this->assertEquals($job->getState(), 'Sydney');
        $this->assertEquals($job->getMapper(), $mapper);
        $this->assertEquals($job->getEntityFactory(), $entityFactory);
        $this->assertEquals($job->getRequestBody(), []);
        $this->assertEquals($job->getBaseUrl(), 'Street');
        $this->assertEquals($job->getSuccessResultUrl(), 'Street');
        $this->assertEquals($job->getFailedResultUrl(), 'Street');
        $this->assertEquals($job->getUnprocessedResultUrl(), 'Street');
    }
}
