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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Debug\Stopwatch;

class RequestListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', 0),
            'request.complete' => array('onRequestComplete', 255),
        );
    }

    protected $stopwatch;

    public function __construct(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function onRequestBeforeSend()
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->start('Guzzle');
        }
    }

    public function onRequestComplete()
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->stop('Guzzle');
        }
    }
}
