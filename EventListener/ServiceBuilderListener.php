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

class ServiceBuilderListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'service_builder.create_client' => 'onCreateClient',
        );
    }

    protected $listeners = array();

    public function addClientListener(EventSubscriberInterface $listener)
    {
        $this->listeners[] = $listener;
    }

    public function onCreateClient(Event $event)
    {
        foreach ($this->listeners as $listener) {
            $event['client']->addSubscriber($listener);
        }
    }
}
