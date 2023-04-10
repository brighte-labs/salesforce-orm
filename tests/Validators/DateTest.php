<?php
namespace SalesforceTest\Validators;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Opportunity;
use Salesforce\ORM\Mapper;
use Salesforce\ORM\Validators\Date;
use Salesforce\ORM\Annotation\Date As AnnotationDate;

class dateTest extends TestCase
{
    /** @var $dateClass Date */
    protected $dateClass;
    /** @var $opportunityEntity Opportunity */
    protected $opportunityEntity;
    /** @var $mapper Mapper */
    protected $mapper;
    /** @var $reader Reader */
    protected $reader;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new AnnotationReader();
        $this->mapper = new Mapper($this->reader);
        $this->opportunityEntity = new Opportunity();
        $this->mapper->setPropertyValueByName($this->opportunityEntity, "closeDate", "2000-01-01");
        $this->dateClass = new Date($this->mapper);
    }

    public function testDate()
    {
        $AnnotationDateInterface = new AnnotationDate(['name' => 'closeDate', 'value' => '2000-01-01']);
        $closeDateProperty = new \ReflectionProperty(Opportunity::class, "closeDate");
        $result = $this->dateClass->validate($this->opportunityEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertTrue($result);

        // empty date
        $this->mapper->setPropertyValueByName($this->opportunityEntity, "closeDate", null);
        $this->dateClass = new Date($this->mapper);
        $result = $this->dateClass->validate($this->opportunityEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertTrue($result);
    }
}
