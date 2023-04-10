<?php

namespace SalesforceTest\Job\Bulk;

use PHPUnit\Framework\TestCase;
use Salesforce\Job\Bulk\DeleteJob;
use Salesforce\Job\Exception\JobException;
use Salesforce\ORM\Exception\EntityException;

class DeleteJobTest extends TestCase
{
    /** @var $job DeleteJob */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new DeleteJob();
    }

    /**
     * Test properties.
     */
    public function testProperties()
    {
        // set properties
        $job = $this->job;
        $job->setCsvData(['test']);

        // get properties
        $this->assertEquals($job->getCsvData(), ['test']);
    }

    /**
     * @throws \Salesforce\Job\Exception\JobException
     * @throws \Salesforce\ORM\Exception\EntityException
     */
    public function testValidate()
    {
        $this->job->setCsvData([['Id'], ['test']]);
        $this->assertEquals(true, $this->job->validate());
    }

    /**
     * Test Exception data missing
     */
    public function testValidateThrowDataMissingException()
    {
        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), JobException::MSG_JOB_DATA_MISSING);
        }
    }

    /**
     * Test Exception column missing
     */
    public function testValidateThrowMissingColumnException()
    {
        $this->job->setCsvData([['Id'], ['test', 'test2']]);

        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), EntityException::MGS_CSV_ROW_COUNT_MISMATCH);
        }
    }

    /**
     * Test Exception should be on column only
     */
    public function testValidateThrowMoreThanOneColumnException()
    {
        $this->job->setCsvData([['Id', 'test2'], ['test', 'test2']]);

        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), JobException::MSG_OPERATION_DELETE_VALIDATION_FAILED);
        }
    }
}
