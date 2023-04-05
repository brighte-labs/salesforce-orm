<?php
namespace SalesforceTest\ORM;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Salesforce\Client\Client;
use Salesforce\Client\Connection;
use Salesforce\Client\ResponseCodes;
use Salesforce\Entity\Account;
use Salesforce\Event\EventDispatcherInterface;
use Salesforce\ORM\Annotation\OneToOne;
use Salesforce\ORM\EntityManager;
use Salesforce\ORM\Exception\EntityException;
use Salesforce\ORM\FieldNames;
use Salesforce\ORM\Mapper;
use  Salesforce\ORM\RelationHandles\OneToMany;

class EntityManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $sfClient;
    /** @var Connection */
    protected $connection;
    /** @var Mapper */
    protected $mapper;
    /** @var EntityManager */
    protected $entityManager;
    /** @var config */
    protected $config;
    /** @var Reader */
    protected $reader;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = ["clientId" => "***", "clientSecret" => "***", "path" => "***", 'username' => '***', "password" => "***", "apiVersion" => "***", 'apexEndPoint' => 'http://localhost/apex'];
        $this->connection = new Connection($this->config);
        $this->sfClient = $this->createMock(Client::class);
        $this->sfClient->method('query')->willReturn(['query stuff']);

        $this->connection->setClient($this->sfClient);
        $this->reader = new AnnotationReader();
        $this->mapper = new Mapper($this->reader);
        $this->entityManager = new EntityManager($this->connection, $this->mapper);
    }

    public function testFind()
    {
        // empty class
        $id = "someId";
        $class = "";
        try {
            $this->entityManager->find($class, $id);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_EMPTY_CLASS_NAME);
        }

        $id = "";
        $class = Account::class;
        try {
            $this->entityManager->find($class, $id);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_ID_IS_NOT_PROVIDED);
        }

        $id = "someId";
        $object = $this->mapper->object($class);
        $objectType = $this->mapper->getObjectType($object);
        $this->sfClient->expects($this->atLeastOnce())->method("findObject")->with($objectType, $id)->willReturn(true);
        $this->entityManager->find($class, $id);
    }

    public function testFindBy()
    {
        $class = Account::class;
        $result = $this->entityManager->findBy($class);
        $this->assertSame(get_class($result[0]), Account::class);

        $class = "";
        try {
            $this->entityManager->findBy($class);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_EMPTY_CLASS_NAME);
        }
    }

    public function testFindAll()
    {
        $class = Account::class;
        $result = $this->entityManager->findAll($class);
        $this->assertSame(get_class($result[0]), Account::class);

        $class = "";
        try {
            $this->entityManager->findAll($class);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_EMPTY_CLASS_NAME);
        }
    }

    public function testCount()
    {
        $countResultArray = ['expr0' => ['sql query']];
        $sfClient = $this->createMock(Client::class);
        $sfClient->method('query')->willReturn([$countResultArray]);
        $this->connection->setClient($sfClient);
        $mapper = new Mapper();
        $entityManager = new EntityManager($this->connection, $mapper);
        $class = Account::class;

        $result = $entityManager->count($class);
        $this->assertSame($result[0], 'sql query');

        $class = "";
        try {
            $entityManager->count($class);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_EMPTY_CLASS_NAME);
        }

        $sfClient = $this->createMock(Client::class);
        $sfClient->method('query')->willReturn(false);
        $this->connection->setClient($sfClient);
        $mapper = new Mapper();
        $entityManager = new EntityManager($this->connection, $mapper);

        $class = Account::class;
        $result = $entityManager->count($class);
        $this->assertFalse($result);
    }

    public function testUpdate()
    {
        $account = new Account();
        $account->setName("Ken");
        $account->setId("12345");
        $mapper = new Mapper();
        $this->entityManager->setMapper($mapper);
        $mapper = $this->entityManager->getMapper();

        $objectType = $mapper->getObjectType($account);
        $data = $mapper->toArray($account);

        if (isset($data[FieldNames::SF_FIELD_ID])) {
            unset($data[FieldNames::SF_FIELD_ID]);
        };

        $this->sfClient->expects($this->once())->method("updateObject")->with($objectType, $account->getId(), $data)->willReturn(ResponseCodes::HTTP_NO_CONTENT);
        $this->entityManager->save($account);
    }

    public function testSave()
    {
        $account = new Account();
        $account->setName("Test");
        $objectType = $this->mapper->getObjectType($account);
        $data = $this->configData();
        $this->sfClient->expects($this->once())->method("createObject")->with($objectType, $data)->willReturn(true);
        $this->entityManager->save($account);
    }

    public function testUpdateEntity()
    {
        // empty class
        $data = [];
        $account = new Account();
        $account->setId("12345");
        $objectType = $this->mapper->getObjectType($account);
        try {
            $this->entityManager->update($account, $data);
        } catch (\Exception $e) {
            $this->assertSame($e->getMessage(), EntityException::MGS_ID_IS_NOT_PROVIDED);
        }


        $data = $this->configData();

        $this->connection->setClient($this->sfClient);

        $this->sfClient->expects($this->atLeastOnce())->method('updateObject')->with($objectType, $account->getId(), $data)->willReturn([ResponseCodes::HTTP_OK]);
        $this->connection->setClient($this->sfClient);
        $this->entityManager->setConnection($this->connection);
        $connectionResult = $this->entityManager->getConnection();

        $result = $this->entityManager->update($account, $data);
        $this->assertTrue($result);

        // Update empty data : Return True
        $data = [];
        $result = $this->entityManager->update($account, $data);
        $this->assertTrue($result);

        // Patched already
        $data = $this->configData();

        $account->setIsPatched(false);
        $result = $this->entityManager->update($account, $data);
        $this->assertTrue($result);
    }

    public function testGetEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->entityManager->setEventDispatcher($eventDispatcher);
        $eventDispatcher = $this->entityManager->getEventDispatcher();
        $this->assertNull($eventDispatcher);
    }

    public function configData()
    {
        $data = [
            'Name' => 'Test',
            'Website' => null,
            'BillingCity' => null,
            'BillingCountry' => null,
            'BillingPostalCode' => null,
            'BillingState' => null,
            'BillingStreet' => null,
            'OwnerId' => null,
        ];

        return $data;
    }

    public function testGetRepository()
    {
        $result = $this->entityManager->getRepository(Account::class);
        $this->assertSame($result->getClassName(), Account::class);
    }

    public function testEagerLoad()
    {
        $RelationInterface = $this->createMock(OneToOne::class);
        $RelationHandleInterface = $this->createMock(OneToMany::class);
        $reflection = $this->createMock(\ReflectionProperty::class);

        $collection = [
            [
                'property' => $reflection,
                'annotation' => $RelationInterface
            ]
        ];

        $account = $this->createPartialMock(Account::class, ['getEagerLoad']);
        $account->expects($this->atLeastOnce())->method('getEagerLoad')->willReturn($collection);
        $RelationInterface->expects($this->once())->method('getHandler')->willReturn($RelationHandleInterface);;
        $RelationHandleInterface->expects($this->once())->method('handle');
        $this->entityManager->eagerLoad($account);
    }

    public function testFailToFind()
    {
        $sfClient = $this->createMock(Client::class);
        $sfClient->method('query')->willReturn(false);

        $this->connection->setClient($sfClient);
        $class = Account::class;
        $result = $this->entityManager->findBy($class);
        $this->assertEmpty($result);

        $result = $this->entityManager->findAll($class);
        $this->assertEmpty($result);
    }
}
