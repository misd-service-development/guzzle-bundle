<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Fixtures\ConcreteCommandClient\Command;

use JMS\Serializer\SerializerInterface;
use Misd\GuzzleBundle\Service\Command\JMSSerializerAwareCommandInterface;

class GetPersonWithSerializer extends GetPerson implements JMSSerializerAwareCommandInterface
{
    public $serializer;

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function process()
    {
        $this->result = $this->serializer->deserialize(
            $this->request->getResponse()->getBody(true),
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            'xml'
        );
    }
}
