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
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Adds request length details to the Symfony2 Profiler timeline.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 * @author Ben Davies <ben.davies@gmail.com>
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
     * Cache of request hashes against their open order
     *
     * @var array
     */
    protected $requests = array();

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
     * Starts the stopwatch.
     *
     * @param Event $e
     */
    public function onRequestBeforeSend(Event $e)
    {
        if (null !== $this->stopwatch) {
            $this->start($e);
        }
    }

    /**
     * Stops the stopwatch.
     * @param Event $e
     */
    public function onRequestComplete(Event $e)
    {
        if (null !== $this->stopwatch) {
            $this->stop($e);
        }
    }

    /**
     * @param Event $e
     */
    private function start(Event $e)
    {
        $request = $e['request'];
        $this->requests[$this->hash($request)] = count($this->requests) + 1;
        $name = $this->getEventName($request);

        $this->stopwatch->start($name, 'guzzle');
    }

    /**
     * @param Event $e
     */
    private function stop(Event $e)
    {
        $request = $e['request'];
        $name = $this->getEventName($request);

        $this->stopwatch->stop($name);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function hash(Request $request)
    {
        return spl_object_hash($request);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getEventName(Request $request)
    {
        return sprintf('[%d] %s %s', $this->requests[$this->hash($request)], $request->getMethod(), urldecode($request->getPath()));
    }
}
