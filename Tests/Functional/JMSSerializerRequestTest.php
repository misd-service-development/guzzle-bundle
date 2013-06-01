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
use Misd\GuzzleBundle\Tests\Fixtures\Person;

class JMSSerializerRequestTest extends TestCase
{
    public function testGetPersonXmlRequestWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('AddPerson', array('person' => self::person1()));
        $request = $command->prepare();

        $this->assertEquals('application/xml', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person1(), 'xml')
        );
    }

    public function testGetPersonJsonRequestWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('AddPersonJson', array('person' => self::person1()));
        $request = $command->prepare();

        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person1(), 'json')
        );
    }

    public function testGetPersonYamlRequestWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('AddPersonYaml', array('person' => self::person1()));
        self::$mock->addResponse(new Response(201));
        $request = $command->prepare();

        $this->assertEquals('application/yaml', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person1(), 'yml')
        );
    }

    public function testUpdatePeopleRequest()
    {
        $people = array(self::person1(), self::person2());

        $client = self::getClient('JMSSerializerBundle');

        $command = $client->getCommand('UpdatePeopleJson', array('people' => $people));
        $request = $command->prepare();

        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize($people, 'json')
        );
    }

    public function testUpdatePeopleWithFilterRequest()
    {
        $people = array('foo' => 'bar', array('baz', 'qux'));

        $client = self::getClient('JMSSerializerBundle');

        $command = $client->getCommand('UpdatePeopleJsonWithFilter', array('people' => $people));
        $request = $command->prepare();

        $this->assertEquals($request->getBody(), json_encode($people));
    }

    protected function person1()
    {
        $person = new Person();
        $person->id = 1;
        $person->firstName = 'Foo';
        $person->familyName = 'Bar';

        return $person;
    }

    protected function person2()
    {
        $person = new Person();
        $person->id = 2;
        $person->firstName = 'Baz';
        $person->familyName = 'Qux';

        return $person;
    }
}
