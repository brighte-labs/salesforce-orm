<?php

namespace SalesforceTest\Job;

use PHPUnit\Framework\TestCase;
use Salesforce\Job\Job;
use Salesforce\Job\JobResult;

class JobResultTest extends TestCase
{
    /** @var $jobResult JobResult */
    protected $jobResult;

    public function setUp(): void
    {
        parent::setUp();
        $this->jobResult = new JobResult();
    }

    public function testProperties()
    {
        // set properties
        $jobResult = $this->jobResult;
        $jobResult->setSuccessfulResult(['success']);
        $jobResult->setUnprocessedRecords(['unprocessed']);
        $jobResult->setFailedResult(['failed']);

        // get properties
        $this->assertEquals($jobResult->getSuccessfulResult(), ['success']);
        $this->assertEquals($jobResult->getUnprocessedRecords(), ['unprocessed']);
        $this->assertEquals($jobResult->getFailedResult(), ['failed']);
        $this->assertEquals($jobResult->getAll(), [
            Job::JOB_RESULT_SUCCESSFUL => ['success'],
            Job::JOB_RESULT_FAILED => ['failed'],
            Job::JOB_RESULT_UNPROCESSED => ['unprocessed']
        ]);
    }
}
