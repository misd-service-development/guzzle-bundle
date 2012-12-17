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

class JMSSerializerAwareCommandTest extends TestCase
{
    protected static $client;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $client = self::getContainer('JMSSerializerBundle')->get('guzzle.service_builder')->get('concrete');
        $client->addSubscriber(self::$mock);

        self::$client = $client;
    }

    public function testCommandWithoutSerializer()
    {
        $client = self::$client;
        self::$mock->addResponse(self::xmlResponse());

        $command = $client->getCommand('GetPerson');

        $this->assertInstanceOf('SimpleXMLElement', $client->execute($command));
    }

    public function testCommandWithSerializer()
    {
        $client = self::$client;
        self::$mock->addResponse(self::xmlResponse());

        $command = $client->getCommand('GetPersonWithSerializer');

        $this->assertInstanceOf('JMS\Serializer\SerializerInterface', $command->serializer);
        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $client->execute($command));
    }

    protected function xmlResponse($id = 1)
    {
        return Response::fromMessage(
            'HTTP/1.1 200 OK
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test
Content-Type: application/xml

<?xml version="1.0" encoding="UTF-8"?>
<person id="' . $id . '">
<name>Foo</name>
<family-name>Bar</family-name>
</person>
'
        );
    }
}
