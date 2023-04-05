<?php
namespace SalesforceTest\Job\Bulk;

use PHPUnit\Framework\TestCase;
use Salesforce\Job\Bulk\QueryJob;
use Salesforce\Job\Exception\JobException;
use Salesforce\Job\Job;

class QueryJobTest extends TestCase
{
    /** @var $job QueryJob */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new QueryJob();
    }

    /**
     * Test porperties
     * @throws JobException
     */
    public function testProperties()
    {
        // set properties
        $job = $this->job;
        $job->setQuery('Test');

        // get properties
        $this->assertEquals($job->getQuery(), 'Test');
        $this->assertTrue(array_key_exists(Job::JOB_FIELD_QUERY, $job->getRequestBody()));

        $job->setQuery('');
        try {
            $job->getRequestBody();
        } catch (\Exception $e) {
            $this->assertStringContainsString(JobException::MSG_FAILED_QUERY_REQUIRED_FOR_QUERY_JOB, $e->getMessage());
        }
    }
}
