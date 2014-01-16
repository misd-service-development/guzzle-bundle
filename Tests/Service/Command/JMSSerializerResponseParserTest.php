<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Service\Command;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Description\OperationInterface;
use JMS\Serializer\DeserializationContext;
use Misd\GuzzleBundle\Service\Command\JMSSerializerResponseParser;

class JMSSerializerResponseParserTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeContextConfiguration()
    {
        $expectedContext = DeserializationContext::create();
        $expectedContext->setGroups('group');
        $expectedContext->setVersion(1);
        $expectedContext->enableMaxDepthChecks();

        $operation = $this->getMock('Guzzle\Service\Description\OperationInterface');
        $operation->expects($this->any())->method('getResponseType')->will($this->returnValue(OperationInterface::TYPE_CLASS));
        $operation->expects($this->any())->method('getResponseClass')->will($this->returnValue('ResponseClass'));

        $dataMap = array(
            array('jms_serializer.groups', 'group'),
            array('jms_serializer.version', 1),
            array('jms_serializer.max_depth_checks', true)
        );

        $operation->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($dataMap));

        $command = $this->getMock('Guzzle\Service\Command\CommandInterface');
        $command->expects($this->any())->method('getOperation')->will($this->returnValue($operation));

        $response = new Response(200);;
        $response->setBody('body');

        $serializer = $this->getMock('JMS\Serializer\SerializerInterface');
        $serializer->expects($this->once())->method('deserialize')
            ->with('body', 'ResponseClass', 'json', $this->equalTo($expectedContext));

        $parser = new JMSSerializerResponseParser($serializer, $this->getMock('Guzzle\Service\Command\ResponseParserInterface'));

        $ref = new \ReflectionMethod($parser, 'deserialize');
        $ref->setAccessible(true);

        return $ref->invoke($parser, $command, $response, 'json');
    }
}
