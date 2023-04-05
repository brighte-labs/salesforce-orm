<?php
namespace SalesforceTest\ORM;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\ORM\Exception\MapperException;
use Salesforce\ORM\Mapper;

class MapperTest extends TestCase
{
    protected $reader;
    /** @var Mapper */
    protected $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new AnnotationReader();
        $this->mapper = new Mapper($this->reader);
    }

    public function testGetObjectType()
    {
        $account = new Account();
        $name = $this->mapper->getObjectType($account);
        $this->assertEquals($name, "Account");
    }

    public function testPatch()
    {
        $account = new Account();
        $data = ["Id" => "12346", "Name" => "Ken"];
        $account = $this->mapper->patch($account, $data);
        $this->assertEquals($account->getId(), $data["Id"]);
        $this->assertEquals($account->getName(), $data["Name"]);
    }

    public function testToArray()
    {
        $account = new Account();
        $data = ["Id" => "12346", "Name" => "Ken"];
        $this->mapper->patch($account, $data);
        $array = $this->mapper->toArray($account);
        $this->assertEquals($data["Id"], $array["Id"]);
        $this->assertEquals($data["Name"], $array["Name"]);
    }

    public function testGetPropertyValueByFieldName()
    {
        $account = new Account();
        $data = ["Id" => "12346", "Name" => "Ken"];
        $this->mapper->patch($account, $data);
        $array = $this->mapper->toArray($account);
        $this->mapper->getPropertyValueByFieldName($account, "Name");
        $this->assertEquals($array["Name"], "Ken");

        try {
            $this->mapper->getPropertyValueByFieldName($account, "DoesntExist");
        } catch (MapperException $e) {
            $this->assertEquals($e->getMessage(), MapperException::MSG_NO_FIELD_FOUND ."DoesntExist");
        }
    }

    public function testGetPropertyValueByName()
    {
        $account = new Account();
        $data = ["Id" => "12346", "Name" => "Ken"];
        $this->mapper->patch($account, $data);
        $result = $this->mapper->getPropertyValueByName($account, "name");
        $this->assertEquals($result, 'Ken');
    }

    public function testCheckRequiredValidations()
    {
        $account = new Account();
        $data = [ "Id" => "Ken"];
        $requiredValidations['Id'] = ['property' => 'Id', 'annotation' => 'Id'];
        $account->setRequiredValidations($requiredValidations);
        $this->mapper->patch($account, $data);
        $result = $this->mapper->checkRequiredValidations($account);
        $this->assertTrue($result);


        // Create a stub for the Mapper class.
        $stub = $this->createMock(Mapper::class);
        $stub->method('checkRequiredValidations')
            ->with($this->equalTo($account))
            ->willReturn($account);

        // Calling $stub->checkRequiredValidations() will now return Account
        $this->assertEquals($account, $stub->checkRequiredValidations($account));
    }

    public function testReflectException()
    {
        $account = new Account();
        $reflectionException =  new \ReflectionException(MapperException::MGS_FAILED_TO_CREATE_REFLECT_CLASS);

        // Create a stub for the Mapper class.
        $stub = $this->createPartialMock(Mapper::class, ['reflect']);

        $stub->method('reflect')
            ->with($this->equalTo($account))
            ->willThrowException($reflectionException);

        // Calling $stub->reflect() will now throw exception.
        try {
            $stub->reflect($account);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), MapperException::MGS_FAILED_TO_CREATE_REFLECT_CLASS);
        }
    }
}
