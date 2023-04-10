<?php
namespace SalesforceTest\Validators;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\ORM\Mapper;
use Salesforce\ORM\Validators\Url;
use Salesforce\ORM\Annotation\Url As AnnotationUrl;

class urlTest extends TestCase
{
    /** @var $urlClass Url */
    protected $urlClass;
    /** @var $accountEntity Account */
    protected $accountEntity;
    /** @var $mapper Mapper */
    protected $mapper;
    /** @var $reader Reader */
    protected $reader;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new AnnotationReader();
        $this->mapper = new Mapper($this->reader);
        $this->accountEntity = new Account();
        $this->mapper->setPropertyValueByName($this->accountEntity, 'website', 'http://www.test.com');
        $this->urlClass = new Url($this->mapper);
    }

    public function testUrl()
    {
        $AnnotationDateInterface = new AnnotationUrl(['name' => 'website', 'value' => 'http://www.test.com']);
        $closeDateProperty = new \ReflectionProperty(Account::class, "website");

        $result = $this->urlClass->validate($this->accountEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertEquals($result, 'http://www.test.com');

        // empty url
        $this->mapper->setPropertyValueByName($this->accountEntity, "website", null);
        $this->urlClass = new Url($this->mapper);
        $result = $this->urlClass->validate($this->accountEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertTrue($result);
    }
}
