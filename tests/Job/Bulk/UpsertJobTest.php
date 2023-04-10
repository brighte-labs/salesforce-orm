<?php

namespace SalesforceTest\Job\Bulk;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\Job\Bulk\UpsertJob;
use Salesforce\Job\Exception\JobException;
use Salesforce\Job\Job;
use Salesforce\ORM\Exception\EntityException;
use Salesforce\ORM\Mapper;

class UpsertJobTest extends TestCase
{
    /** @var $job UpsertJob */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new UpsertJob();
        $this->job->setEntity(new Account());
        $this->job->setExternalId('test');
    }

    /**
     * Test properties.
     */
    public function testProperties()
    {
        // set properties
        $job = $this->job;
        $job->setCsvData(['test']);
        $job->setExternalId('test');

        // get properties
        $this->assertEquals($job->getCsvData(), ['test']);
        $this->assertEquals($job->getExternalId(), 'test');
        $this->assertTrue(array_key_exists(Job::JOB_FIELD_EXTERNAL_ID_FIELD_NAME, $job->getRequestBody()));
    }

    /**
     *
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

    /**
     * Test Exception column missing
     */
    public function testValidateThrowMissingIdException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Name', 'Id'], ['test', 'IdTest']]);

        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(JobException::MSG_UPSERT_DATA_CANNOT_HAVE_ID_ASSIGNED, $e->getMessage());
        }

        $this->job->setExternalId('');

        try {
            $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(JobException::MSG_EXTERNAL_ID_FIELD_IS_REQUIRED, $e->getMessage());
        }
    }
}
