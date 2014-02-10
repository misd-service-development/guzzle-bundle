<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\EventListener;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Misd\GuzzleBundle\EventListener\RequestListener;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RequestListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestListener()
    {
        $e = new Event(array('request' => new Request('GET', '/')));
        $e2 = new Event(array('request' => new Request('GET', '/')));

        $stopwatch = $this->getMockBuilder('Symfony\Component\Stopwatch\Stopwatch')
            ->disableOriginalConstructor()
            ->setMethods(array('start', 'stop'))
            ->getMock();

        $stopwatch->expects($this->at(0))
            ->method('start')
            ->with('[1] GET /');
        $stopwatch->expects($this->at(1))
            ->method('start')
            ->with('[2] GET /');

        //simulate request 2 finishing before request 1.
        $stopwatch->expects($this->at(2))
            ->method('stop')
            ->with('[2] GET /');
        $stopwatch->expects($this->at(3))
            ->method('stop')
            ->with('[1] GET /');

        $listener = new RequestListener($stopwatch);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($listener);

        $dispatcher->dispatch('request.before_send', $e);
        $dispatcher->dispatch('request.before_send', $e2);

        $dispatcher->dispatch('request.complete', $e2);
        $dispatcher->dispatch('request.complete', $e);
    }
}
