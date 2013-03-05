<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Functional;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use InvalidArgumentException;

class JMSSerializerResponseTest extends TestCase
{
    public function testGetPersonXmlResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));

        $this->assertInstanceOf('SimpleXMLElement', $client->execute($command));
    }

    public function testGetPersonXmlResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));

        $this->assertInstanceOf('SimpleXMLElement', $client->execute($command));
    }

    public function testGetPersonJsonResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::jsonResponse()));

        $this->assertTrue(is_array($client->execute($command)));
    }

    public function testGetPersonJsonResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::jsonResponse()));

        $this->assertTrue(is_array($client->execute($command)));
    }

    public function testGetPersonUnknownResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::unknownResponse()));

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $client->execute($command));
    }

    public function testGetPersonUnknownResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPerson', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::unknownResponse()));

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $client->execute($command));
    }

    public function testGetPersonClassXmlResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));
        $person = $client->execute($command);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $person);
        $this->assertEquals(1, $person->id);
        $this->assertEquals('Foo', $person->firstName);
        $this->assertEquals('Bar', $person->familyName);
    }

    public function testGetPersonClassXmlResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));

        $this->assertInstanceOf('SimpleXMLElement', $client->execute($command));
    }

    public function testGetPersonClassJsonResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::jsonResponse()));
        $person = $client->execute($command);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $person);
        $this->assertEquals(1, $person->id);
        $this->assertEquals('Foo', $person->firstName);
        $this->assertEquals('Bar', $person->familyName);
    }

    public function testGetPersonClassJsonResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::jsonResponse()));

        $this->assertTrue(is_array($client->execute($command)));
    }

    public function testGetPersonClassUnknownResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::unknownResponse()));

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $client->execute($command));
    }

    public function testGetPersonClassUnknownResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPersonClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::unknownResponse()));

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $client->execute($command));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPersonInvalidClassXmlResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPersonInvalidClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));
        $client->execute($command);
    }

    public function testGetPersonInvalidClassXmlResponseWithoutSerializer()
    {
        $client = self::getClient('Basic');
        $command = $client->getCommand('GetPersonInvalidClass', array('id' => 1));
        self::$mock->addResponse(Response::fromMessage(self::xmlResponse()));

        $this->assertInstanceOf('SimpleXMLElement', $client->execute($command));
    }

    public function testGetPersonClassArrayJsonResponseWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('GetPersonClassArray');
        self::$mock->addResponse(Response::fromMessage(self::jsonArrayResponse()));
        $people = $client->execute($command);

        $this->assertTrue(is_array($people));
        $this->assertCount(2, $people);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $people[0]);
        $this->assertEquals(1, $people[0]->id);
        $this->assertEquals('Foo', $people[0]->firstName);
        $this->assertEquals('Bar', $people[0]->familyName);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $people[1]);
        $this->assertEquals(2, $people[1]->id);
        $this->assertEquals('Baz', $people[1]->firstName);
        $this->assertEquals('Qux', $people[1]->familyName);
    }

    /**
     * @var Client[]
     */
    protected static $clients = array();
    /**
     * @var MockPlugin
     */
    protected static $mock;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$mock = new MockPlugin();

        foreach (self::getTestCases() as $testCase) {
            $client = self::getContainer($testCase)->get('guzzle.client');
            $client->setDescription(ServiceDescription::factory(__DIR__ . '/../Fixtures/config/client.json'));
            $client->addSubscriber(self::$mock);
            self::$clients[$testCase] = $client;
        }
    }

    protected static function getClient($testCase)
    {
        if (false === isset(self::$clients[$testCase])) {
            throw new InvalidArgumentException(sprintf('Unknown testCase %s', $testCase));
        }

        return self::$clients[$testCase];
    }

    protected function xmlResponse()
    {
        return <<<EOT
HTTP/1.1 200 OK
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test
Content-Type: application/xml

<?xml version="1.0" encoding="UTF-8"?>
<person id="1">
<name>Foo</name>
<family-name>Bar</family-name>
</person>
EOT;
    }

    protected function jsonResponse()
    {
        return <<<EOT
HTTP/1.1 200 OK
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test
Content-Type: application/json

{"id":1,"name":"Foo","family-name":"Bar"}
EOT;
    }

    protected function jsonArrayResponse()
    {
        return <<<EOT
HTTP/1.1 200 OK
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test
Content-Type: application/json

[{"id":1,"name":"Foo","family-name":"Bar"},{"id":2,"name":"Baz","family-name":"Qux"}]
EOT;
    }

    protected function unknownResponse()
    {
        return <<<EOT
HTTP/1.1 200 OK
Date: Sun, 1 Jan 2012 00:00:00 GMT
Connection: close
Server: Test
Content-Type: application/foo

ID = 1; Name = Foo; Family name = Bar.
EOT;
    }
}
