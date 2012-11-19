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
use Guzzle\Service\Command\ResponseParserInterface;
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
     * @param RequestVisitorInterface $requestBodyVisitor Request body visitor.
     * @param ResponseParserInterface $responseParser     Response parser.
     */
    public function __construct(RequestVisitorInterface $requestBodyVisitor, ResponseParserInterface $responseParser)
    {
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
        $event['command']->getRequestSerializer()->addVisitor('body', $this->requestBodyVisitor);
        $event['command']->setResponseParser($this->responseParser);
    }
}
