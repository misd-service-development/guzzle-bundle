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
use JMS\Serializer\Serializer;

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
     * @var Serializer|null
     */
    protected $serializer;

    /**
     * Constructor.
     *
     * @param Serializer|null $serializer Serializer, or null if not used.
     */
    public function __construct(Serializer $serializer = null)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function parseForContentType(AbstractCommand $command, Response $response, $contentType)
    {
        if (null !== $this->serializer) {
            if (false !== stripos($contentType, 'json')) {
                $serializerContentType = 'json';
            } elseif (false !== stripos($contentType, 'xml')) {
                $serializerContentType = 'xml';
            } else {
                $serializerContentType = null;
            }

            if (null !== $serializerContentType && class_exists($command->getOperation()->getResponseClass())) {
                return $this->serializer->deserialize(
                    $response->getBody(),
                    $command->getOperation()->getResponseClass(),
                    $serializerContentType
                );
            }
        }

        return parent::parseForContentType($command, $response, $contentType);
    }
}
