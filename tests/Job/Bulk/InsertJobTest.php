<?php

namespace SalesforceTest\Job\Bulk;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\Job\Bulk\DeleteJob;
use Salesforce\Job\Bulk\InsertJob;
use Salesforce\Job\Exception\JobException;
use Salesforce\Job\Job;
use Salesforce\ORM\Exception\EntityException;
use Salesforce\ORM\Mapper;

class InsertJobTest extends TestCase
{
    /** @var $job DeleteJob */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new InsertJob();
        $this->job->setEntity(new Account());
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
        $this->job->setCsvData([['Name'], ['test']]);
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
     * Test Exception column missing
     */
    public function testValidateThrowRequiredPropertyException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Name'], ['test']]);

        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredProperties')->willReturn(true);

        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredValidations')->willReturn([Job::JOB_FIELD_STATE => Job::STATE_UPLOAD_COMPLETE]);

        try {
            $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(EntityException::MGS_REQUIRED_VALIDATIONS, $e->getMessage());
        }
    }

    /**
     * Test Exception column missing
     */
    public function testValidateThrowRequiredValueException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Name'], ['test']]);

        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredProperties')->willReturn([Job::JOB_FIELD_STATE => Job::STATE_UPLOAD_COMPLETE]);


        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(EntityException::MGS_REQUIRED_PROPERTIES, $e->getMessage());
        }
    }

    /**
     * Test Exception column missing
     */
    public function testValidateThrowEmptyNoneProtechDataException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Name'], ['test']]);

        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredProperties')->willReturn(true);
        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredValidations')->willReturn(true);
        $this->job->getMapper()->expects($this->exactly(1))->method('checkNoneProtectionData')->willReturn(false);

        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(EntityException::MGS_EMPTY_NONE_PROTECTION_DATA, $e->getMessage());
        }
    }
}
