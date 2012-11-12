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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add the bundle's listeners to Guzzle clients created through the Guzzle
 * service builder.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class ServiceBuilderListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'service_builder.create_client' => 'onCreateClient',
        );
    }

    /**
     * @var EventSubscriberInterface[]
     */
    protected $listeners = array();

    /**
     * Add a listener to the set.
     *
     * @param EventSubscriberInterface $listener
     */
    public function addClientListener(EventSubscriberInterface $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Adds the listeners to the client.
     *
     * @param Event $event
     */
    public function onCreateClient(Event $event)
    {
        foreach ($this->listeners as $listener) {
            $event['client']->addSubscriber($listener);
        }
    }
}
