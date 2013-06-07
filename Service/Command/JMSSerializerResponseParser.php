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
use Guzzle\Service\Command\ResponseParserInterface;
use Guzzle\Service\Description\OperationInterface;
use Guzzle\Service\Command\CommandInterface;
use JMS\Serializer\SerializerInterface;

/**
 * JMSSerializer-enabled response parser.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class JMSSerializerResponseParser implements ResponseParserInterface
{
    /**
     * Serializer.
     *
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * Fallback parser.
     *
     * @var ResponseParserInterface
     */
    protected $fallback;

    /**
     * Constructor.
     *
     * @param SerializerInterface|null $serializer Serializer, or null if not used.
     * @param ResponseParserInterface  $fallback   Fallback parser to use if the serializer cannot parse the response.
     */
    public function __construct(SerializerInterface $serializer = null, ResponseParserInterface $fallback)
    {
        $this->serializer = $serializer;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(CommandInterface $command)
    {
        $response = $command->getRequest()->getResponse();
        $contentType = (string) $response->getHeader('Content-Type');

        return $this->handleParsing($command, $response, $contentType);
    }

    /**
     * Handle the parsing.
     *
     * @param CommandInterface $command     Command.
     * @param Response         $response    Response.
     * @param string           $contentType Content type.
     *
     * @return mixed Returns the result to set on the command.
     */
    protected function handleParsing(CommandInterface $command, Response $response, $contentType)
    {
        $deserialized = $this->deserialize($command, $response, $contentType);

        if (null !== $deserialized) {
            return $deserialized;
        } else {
            return $this->fallback->parse($command);
        }
    }

    /**
     * Deserialize the response.
     *
     * @param CommandInterface $command     Command.
     * @param Response         $response    Response.
     * @param string           $contentType Content type.
     *
     * @return mixed|null Deserialized response, or `null`.
     */
    protected function deserialize(CommandInterface $command, Response $response, $contentType)
    {
        if ($this->serializer) {
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
