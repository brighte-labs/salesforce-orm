<?php
namespace SalesforceTest\ORM;

use Salesforce\Entity\Account;
use Salesforce\ORM\EntityManager;
use Salesforce\ORM\Exception\RepositoryException;
use Salesforce\ORM\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $entityManager;
    /** @var Repository */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->repository = new Repository($this->entityManager);
    }

    public function testFind()
    {
        $class = "Account";
        $id = "12345";
        $this->entityManager->expects($this->atLeastOnce())->method('find')->with($class, $id);
        $this->repository->setClassName($class)->find($id);

        try {
            $class = null;
            $this->repository->setClassName($class)->find($id);
        } catch (\Exception $exception) {
            $this->assertEquals(RepositoryException::MSG_NO_CLASS_NAME_PROVIDED, $exception->getMessage());
        }
    }

    public function testFindBy()
    {
        $class = "Account";
        $where = ["Id" => "12345"];
        $this->entityManager->expects($this->once())->method('findBy');
        $this->repository->setClassName($class)->findBy($where);

        try {
            $class = null;
            $this->repository->setClassName($class)->findBy($where);
        } catch (\Exception $exception) {
            $this->assertEquals(RepositoryException::MSG_NO_CLASS_NAME_PROVIDED, $exception->getMessage());
        }
    }

    public function testFindAll()
    {
        $class = "Account";
        $this->entityManager->expects($this->once())->method('findAll');
        $this->repository->setClassName($class)->findAll();

        try {
            $class = null;
            $this->repository->setClassName($class)->findAll();
        } catch (\Exception $exception) {
            $this->assertEquals(RepositoryException::MSG_NO_CLASS_NAME_PROVIDED, $exception->getMessage());
        }
    }

    public function testCount()
    {
        $class = "Account";
        $this->entityManager->expects($this->once())->method('count');
        $this->repository->setClassName($class)->count();
    }

    public function testGetEntityManager()
    {
        $class = "Account";
        $entityManager = $this->createMock(EntityManager::class);
        $repository = new Repository($entityManager);
        $result = $repository->setClassName($class)->getEntityManager();
        $this->assertTrue($result instanceof $entityManager);
    }

    public function testSetEntityManager()
    {
        $class = "Account";
        $entityManager = $this->createMock(EntityManager::class);
        $repository = new Repository($entityManager);
        $result = $repository->setClassName($class)->setEntityManager($this->createMock(EntityManager::class));
        $this->assertTrue($result instanceof Repository);
    }
}
