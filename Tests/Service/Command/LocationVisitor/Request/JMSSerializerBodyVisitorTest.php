<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Service\Command\LocationVisitor\Request;

use JMS\Serializer\SerializationContext;
use Misd\GuzzleBundle\Service\Command\LocationVisitor\Request\JMSSerializerBodyVisitor;

class JMSSerializerBodyVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeContextConfiguration()
    {
        $expectedContext = SerializationContext::create();
        $expectedContext->setGroups('group');
        $expectedContext->setVersion(1);
        $expectedContext->setSerializeNull(true);
        $expectedContext->enableMaxDepthChecks();

        $parameter = $this->getMock('Guzzle\Service\Description\Parameter');
        $parameter->expects($this->once())->method('getSentAs')->will($this->returnValue('json'));
        $parameter->expects($this->any())->method('filter')->will($this->returnValue(array()));

        $dataMap = array(
            array('jms_serializer.groups', 'group'),
            array('jms_serializer.version', 1),
            array('jms_serializer.serialize_nulls', true),
            array('jms_serializer.max_depth_checks', true)
        );

        $parameter->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($dataMap));

        $command = $this->getMock('Guzzle\Service\Command\CommandInterface');
        $request = $this->getMockBuilder('Guzzle\Http\Message\EntityEnclosingRequest')
            ->disableOriginalConstructor()
            ->getMock();

        $serializer = $this->getMock('JMS\Serializer\SerializerInterface');
        $serializer->expects($this->once())->method('serialize')
            ->with(array(), 'json', $this->equalTo($expectedContext))
            ->will($this->returnValue('serialized'));

        $parser = new JMSSerializerBodyVisitor($serializer, $this->getMock('Guzzle\Service\Command\ResponseParserInterface'));

        $ref = new \ReflectionMethod($parser, 'visit');
        $ref->setAccessible(true);

        return $ref->invoke($parser, $command, $request, $parameter, 'value');
    }
}
