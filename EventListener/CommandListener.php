<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\EventListener;

use Guzzle\Common\Event;
use Guzzle\Service\Command\LocationVisitor\Request\RequestVisitorInterface;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseParserInterface;
use JMS\Serializer\SerializerInterface;
use Misd\GuzzleBundle\Service\Command\JMSSerializerAwareCommandInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Allow commands to use the JMSSerializerBundle.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class CommandListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'client.command.create' => 'onCommandCreate',
        );
    }

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var RequestVisitorInterface
     */
    protected $requestBodyVisitor;

    /**
     * @var ResponseParserInterface
     */
    protected $responseParser;

    /**
     * Constructor.
     *
     * @param SerializerInterface|null $serializer         JMSSerializer, if available.
     * @param RequestVisitorInterface  $requestBodyVisitor Request body visitor.
     * @param ResponseParserInterface  $responseParser     Response parser.
     */
    public function __construct(
        SerializerInterface $serializer = null,
        RequestVisitorInterface $requestBodyVisitor,
        ResponseParserInterface $responseParser
    )
    {
        $this->serializer = $serializer;
        $this->requestBodyVisitor = $requestBodyVisitor;
        $this->responseParser = $responseParser;
    }

    /**
     * Add JMSSerializerBundle-enabled services to commands.
     *
     * @param Event $event Event.
     */
    public function onCommandCreate(Event $event)
    {
        if ($event['command'] instanceof OperationCommand) {
            $event['command']->getRequestSerializer()->addVisitor('body', $this->requestBodyVisitor);
            $event['command']->setResponseParser($this->responseParser);
        }

        if ($event['command'] instanceof JMSSerializerAwareCommandInterface && null !== $this->serializer) {
            $event['command']->setSerializer($this->serializer);
        }
    }
}
