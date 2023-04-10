<?php

namespace SalesforceTest\Job\Bulk;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\Job\Bulk\UpdateJob;
use Salesforce\Job\Exception\JobException;
use Salesforce\Job\Job;
use Salesforce\ORM\Exception\EntityException;
use Salesforce\ORM\Mapper;

class UpdateJobTest extends TestCase
{
    /** @var $job UpdateJob */
    protected $job;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->job = new UpdateJob();
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
     * @throws EntityException
     * @throws JobException
     * @throws \Salesforce\ORM\Exception\MapperException
     */
    public function testValidate()
    {

        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Id'], ['test']]);

        $this->job->getMapper()->expects($this->exactly(1))->method('checkRequiredValidations')->willReturn(true);
        $this->job->getMapper()->expects($this->exactly(1))->method('checkNoneProtectionData')->willReturn(true);

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
    public function testValidateThrowRequiredValueException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Id'], ['test']]);

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
    public function testValidateThrowEmptyNoneProtechDataException()
    {
        $this->job->setMapper($this->createMock(Mapper::class));

        $this->job->setCsvData([['Id'], ['test']]);

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

        $this->job->setCsvData([['Name'], ['test']]);

        try {
             $this->job->validate();
        } catch (\Exception $e) {
            $this->assertStringContainsString(EntityException::MGS_ID_IS_NOT_PROVIDED, $e->getMessage());
        }
    }
}
