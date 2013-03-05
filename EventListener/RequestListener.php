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
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Adds request length details to the Symfony2 Profiler timeline.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RequestListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', 0),
            'request.complete' => array('onRequestComplete', 255),
        );
    }

    /**
     * @var Stopwatch|null
     */
    protected $stopwatch;

    /**
     * Constructor.
     *
     * @param Stopwatch|null $stopwatch
     */
    public function __construct(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Number of open requests.
     *
     * @var int
     */
    protected $open = 0;

    /**
     * Starts the stopwatch.
     */
    public function onRequestBeforeSend()
    {
        if (null !== $this->stopwatch) {
            if (0 === $this->open) {
                $this->stopwatch->start('Guzzle');
            }
            $this->open++;
        }
    }

    /**
     * Stops the stopwatch.
     */
    public function onRequestComplete()
    {
        if (null !== $this->stopwatch) {
            $this->open--;
            if (0 === $this->open) {
                $this->stopwatch->stop('Guzzle');
            }
        }
    }
}
