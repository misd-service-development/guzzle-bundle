<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Service\Command;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\AbstractCommand;
use Guzzle\Service\Command\DefaultResponseParser;
use Guzzle\Service\Description\OperationInterface;
use JMS\Serializer\SerializerInterface;

/**
 * JMSSerializerBundle-enabled response parser.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class JMSSerializerResponseParser extends DefaultResponseParser
{
    /**
     * Serializer.
     *
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * Constructor.
     *
     * @param SerializerInterface|null $serializer Serializer, or null if not used.
     */
    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    protected function handleParsing(AbstractCommand $command, Response $response, $contentType)
    {
        $deserialized = $this->deserialize($command, $response, $contentType);

        return null !== $deserialized ? $deserialized : parent::handleParsing($command, $response, $contentType);
    }

    /**
     * {@inheritdoc}
     *
     * Used in <= Guzzle 3.1.1 (renamed `handleParsing()` in 3.1.2).
     */
    public function parseForContentType(AbstractCommand $command, Response $response, $contentType)
    {
        $deserialized = $this->deserialize($command, $response, $contentType);

        return null !== $deserialized ? $deserialized : parent::parseForContentType($command, $response, $contentType);
    }

    protected function deserialize(AbstractCommand $command, Response $response, $contentType)
    {
        if (null !== $this->serializer) {
            if (false !== stripos($contentType, 'json')) {
                $serializerContentType = 'json';
            } elseif (false !== stripos($contentType, 'xml')) {
                $serializerContentType = 'xml';
            } else {
                $serializerContentType = null;
            }

            if (null !== $serializerContentType &&
                OperationInterface::TYPE_CLASS === $command->getOperation()->getResponseType()
            ) {
                return $this->serializer->deserialize(
                    $response->getBody(),
                    $command->getOperation()->getResponseClass(),
                    $serializerContentType
                );
            }
        }

        return null;
    }
}
