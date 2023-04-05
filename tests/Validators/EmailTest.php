<?php
namespace SalesforceTest\Validators;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Contact;
use Salesforce\ORM\Mapper;
use Salesforce\ORM\Validators\Email;
use Salesforce\ORM\Annotation\Email As AnnotationEmail;

class emailTest extends TestCase
{
    /** @var $emailClass Email */
    protected $emailClass;
    /** @var $contactEntity Contact */
    protected $contactEntity;
    /** @var $mapper Mapper */
    protected $mapper;
    /** @var $reader Reader */
    protected $reader;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new AnnotationReader();
        $this->mapper = new Mapper($this->reader);
        $this->contactEntity = new Contact();
        $this->mapper->setPropertyValueByName($this->contactEntity, "email", "test@brighte.com.au");
        $this->emailClass = new Email($this->mapper);
    }

    public function testDate()
    {
        $AnnotationDateInterface = new AnnotationEmail(['name' => 'Email', 'value' => 'test@brighte.com.au']);
        $closeDateProperty = new \ReflectionProperty(Contact::class, "email");
        $result = $this->emailClass->validate($this->contactEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertEquals($result, 'test@brighte.com.au');

        // empty email
        $this->mapper->setPropertyValueByName($this->contactEntity, "email", null);
        $this->emailClass = new Email($this->mapper);
        $result = $this->emailClass->validate($this->contactEntity, $closeDateProperty, $AnnotationDateInterface);
        $this->assertTrue($result);
    }
}
