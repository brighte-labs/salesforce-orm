<?php

namespace SalesforceTest\Entity;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\OpportunityContactRole;

class OpportunityContactRoleTest extends TestCase
{
    /** @var opportunityContactRole OpportunityContactRole */
    protected $opportunityContactRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->opportunityContactRole = new OpportunityContactRole();
    }

    public function testProperties()
    {
        // set properties
        $opportunityContactRole = $this->opportunityContactRole;
        $opportunityContactRole->setId('121');
        $opportunityContactRole->setContactId('C123');
        $opportunityContactRole->setIsPrimary(true);
        $opportunityContactRole->setRole('Admin');
        $opportunityContactRole->setOpportunityId('1234');

        // get properties
        $this->assertEquals($opportunityContactRole->getId(), '121');
        $this->assertEquals($opportunityContactRole->getContactId(), 'C123');
        $this->assertEquals($opportunityContactRole->getIsPrimary(), true);
        $this->assertEquals($opportunityContactRole->getRole(), 'Admin');
        $this->assertEquals($opportunityContactRole->getOpportunityId(), '1234');
    }
}