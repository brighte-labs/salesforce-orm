<?php

namespace SalesforceTest\Entity;

use PHPUnit\Framework\TestCase;
use Salesforce\Entity\Contact;

class ContactTest extends TestCase
{
    /** @var $contact Contact */
    protected $contact;

    public function setUp(): void
    {
        parent::setUp();
        $this->contact = new Contact();
    }

    public function testProperties()
    {
        // set properties
        $contact = $this->contact;
        $contact->setId('121');
        $contact->setAccountId('12345');
        $contact->setEmail('email@test.com');
        $contact->setFirstName('Fname');
        $contact->setLastName('Lname');
        $contact->setMailingCountry('Australia');
        $contact->setMailingCity('Sydney');
        $contact->setMailingState('NSW');
        $contact->setMailingStreet('StreetName');
        $contact->setMailingPostcode('2210');
        $contact->setPhone('12341234');

        // get properties
        $this->assertEquals($contact->getId(), '121');
        $this->assertEquals($contact->getAccountId(), '12345');
        $this->assertEquals($contact->getEmail(), 'email@test.com');
        $this->assertEquals($contact->getFirstName(), 'Fname');
        $this->assertEquals($contact->getLastName(), 'Lname');
        $this->assertEquals($contact->getMailingCountry(), 'Australia');
        $this->assertEquals($contact->getMailingCity(), 'Sydney');
        $this->assertEquals($contact->getMailingState(), 'NSW');
        $this->assertEquals($contact->getMailingStreet(), 'StreetName');
        $this->assertEquals($contact->getMailingPostcode(), '2210');
        $this->assertEquals($contact->getPhone(), '12341234');
    }
}