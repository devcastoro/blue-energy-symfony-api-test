<?php

namespace Controller;

use function MongoDB\BSON\toJSON;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    /**
     * Post a generic new MeterReads
     * Customer UserTest
     * mpxn 000000 (GAS)
     */
    public function testPostMeterReads()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('POST', '/meter-read', array(
            'customerId'    => "UserTest",
            'mpxn'          => "000000",
            'serialNumber'  => "0000000000",
            'type'          => "TEST",
            'registerId'    => "00000",
            'value'         => "00000"
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Post a new MeterReads that returns ELECTRICITY readType
     * Customer UserTest
     * mpxn 111111111111111111111 (ELECTRICITY)
     */
    public function testPostMeterReadsMprn()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('POST', '/meter-read', array(
            'customerId'    => "UserTest",
            'mpxn'          => "111111111111111111111",
            'serialNumber'  => "0000000000",
            'type'          => "TEST",
            'registerId'    => "00000",
            'value'         => "00000"
        ));

        $content = json_decode($client->getResponse()->getContent());

        $this->assertEquals("ELECTRICITY", $content->readType);
    }

    /**
     * Post a new MeterReads that returns GAS readType
     * Customer UserTest
     * mpxn 000000 (GAS)
     */
    public function testPostMeterReadsMpan()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('POST', '/meter-read', array(
            'customerId'    => "UserTest",
            'mpxn'          => "222222",
            'serialNumber'  => "0000000000",
            'type'          => "TEST",
            'registerId'    => "00000",
            'value'         => "00000"
        ));

        $content = json_decode($client->getResponse()->getContent());

        $this->assertEquals("GAS", $content->readType);
    }

    /**
     * Get all MeterReads for the UserTest
     * Customer UserTest
     * mpxn 000000 (GAS)
     */
    public function testGetMeterReads()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('GET', '/meter-read', array(
            'customerId'    => "UserTest",
            'mpxn'          => "000000"
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Get MeterReads from not registered Customer
     * Customer NotRegisteredCustomer
     * mpxn 000000 (GAS)
     */
    public function testGetNotRegisteredCustomer()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('GET', '/meter-read', array(
            'customerId'    => "NotRegisteredCustomer",
            'mpxn'          => "0000000"
        ));

        $this->assertEquals('"Customer NotRegisteredCustomer not registered in DB"', $client->getResponse()->getContent());
    }

    /**
     * Get MeterReads from registered Customer that haven't a record with a specific mpxn
     * Customer UserTest
     * mpxn 111111 (GAS)
     */
    public function testGetRegisteredCustomerWithoutMeter()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        $client->request('GET', '/meter-read', array(
            'customerId'    => "UserTest",
            'mpxn'          => "111111"
        ));

        $this->assertEquals('"MPRN or MPAN: 111111 not registered in DB or not associated anymore to the customerId provided"', $client->getResponse()->getContent());
    }
}