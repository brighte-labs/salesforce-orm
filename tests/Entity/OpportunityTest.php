<?php

namespace SalesforceTest\Entity;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Opportunity;

class OpportunityTest extends TestCase
{
    /** @var opportunity Opportunity */
    protected $opportunity;

    public function setUp(): void
    {
        parent::setUp();
        $this->opportunity = new Opportunity();
    }

    public function testProperties()
    {
        // set properties
        $opportunity = $this->opportunity;
        $opportunity->setId('121');
        $opportunity->setName('Name');
        $opportunity->setOwnerId('Owner123');
        $opportunity->setAccountId('12345');
        $opportunity->setCloseDate('2000/01/01');
        $opportunity->setRecordTypeId('Type');
        $opportunity->setStage('Stage');

        // get properties
        $this->assertEquals($opportunity->getId(), '121');
        $this->assertEquals($opportunity->getName(), 'Name');
        $this->assertEquals($opportunity->getOwnerId(), 'Owner123');
        $this->assertEquals($opportunity->getAccountId(), '12345');
        $this->assertEquals($opportunity->getCloseDate(), '2000/01/01');
        $this->assertEquals($opportunity->getRecordTypeId(), 'Type');
        $this->assertEquals($opportunity->getStage(), 'Stage');
    }
}