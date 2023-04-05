<?php
namespace SalesforceTest\ORM;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;
use Salesforce\ORM\EntityFactory;
use Salesforce\ORM\Mapper;

class EntityFactoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mapper;
    /** @var EntityFactory */
    protected $entityFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = $this->createMock(Mapper::class);
        $this->entityFactory = new EntityFactory($this->mapper);
        $this->entityFactory->setMapper($this->mapper);
        $mapper = $this->entityFactory->getMapper();
        $this->assertEquals($mapper, $this->mapper);
    }

    public function testNew()
    {
        $class = "Account";
        $data = ["Id" => "12345"];
        $this->mapper->expects($this->once())->method('object')->with($class)->willReturn(new Account());
        $this->entityFactory->new($class, $data);
    }

    public function testPatch()
    {
        $account = new Account();
        $data = ["Id" => "12345"];
        $this->mapper->expects($this->once())->method('patch')->with($account, $data)->willReturn(new Account());
        $this->entityFactory->patch($account, $data);
    }
}
