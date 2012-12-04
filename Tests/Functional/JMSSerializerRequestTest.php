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
        $command = $client->getCommand('AddPerson', array('person' => self::person()));
        self::$mock->addResponse(Response::fromMessage(self::response()));
        $response = $client->execute($command);
        $request = $response->getRequest();

        $this->assertEquals('application/xml', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person(), 'xml')
        );
    }

    public function testGetPersonJsonRequestWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('AddPersonJson', array('person' => self::person()));
        self::$mock->addResponse(Response::fromMessage(self::response()));
        $response = $client->execute($command);
        $request = $response->getRequest();

        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person(), 'json')
        );
    }

    public function testGetPersonYamlRequestWithSerializer()
    {
        $client = self::getClient('JMSSerializerBundle');
        $command = $client->getCommand('AddPersonYaml', array('person' => self::person()));
        self::$mock->addResponse(Response::fromMessage(self::response()));
        $response = $client->execute($command);
        $request = $response->getRequest();

        $this->assertEquals('application/yaml', $request->getHeader('Content-Type'));
        $this->assertEquals(
            $request->getBody(),
            self::getContainer('JMSSerializerBundle')->get('serializer')->serialize(self::person(), 'yml')
        );
    }

    protected function person()
    {
        $person = new Person();
        $person->id = 1;
        $person->firstName = 'Foo';
        $person->familyName = 'Bar';

        return $person;
    }

    protected function response()
    {
        return <<<EOT
HTTP/1.1 201 Created
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test

EOT;
    }
}
