<?php

namespace SalesforceTest\Entity;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Account;

class AccountTest extends TestCase
{
    /** @var $account Account */
    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new Account();
    }

    public function testProperties()
    {
        // set properties
        $account = $this->account;
        $account->setId('121');
        $account->setName('Name');
        $account->setOwnerId('Owner123');
        $account->setWebsite('http://www.test.com.au');
        $account->setBillingCity('Sydney');
        $account->setBillingCountry('Australia');
        $account->setBillingPostcode('2000');
        $account->setBillingState('NSW');
        $account->setBillingStreet('Street');

        // get properties
        $this->assertEquals($account->getId(), '121');
        $this->assertEquals($account->getName(), 'Name');
        $this->assertEquals($account->getOwnerId(), 'Owner123');
        $this->assertEquals($account->getWebsite(), 'http://www.test.com.au');
        $this->assertEquals($account->getBillingCity(), 'Sydney');
        $this->assertEquals($account->getBillingCountry(), 'Australia');
        $this->assertEquals($account->getBillingPostcode(), '2000');
        $this->assertEquals($account->getBillingState(), 'NSW');
        $this->assertEquals($account->getBillingStreet(), 'Street');
    }
}