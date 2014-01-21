<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\HttpFoundation\Response as SfResponse;

use Misd\GuzzleBundle\DataCollector\GuzzleDataCollector;
use Misd\GuzzleBundle\Tests\Stubs\HistoryPluginStub;

/**
 * Guzzle data collector functional test
 *
 * @author Ludovic Fleury <ludo.fleury@gmail.com>
 */
class GuzzleDataCollectorTest extends TestCase
{
    public function testNoDuplicateLogs()
    {
        $historyPlugin = new HistoryPluginStub($this->getStubCalls());
        $collector = new GuzzleDataCollector($historyPlugin);

        $collector->collect(new SfRequest(), new SfResponse(), null);
        $collector->collect(new SfRequest(), new SfResponse(), null);

        $this->assertCount(1, $collector->getCalls());
    }

    /**
     * Mock a history plugin journal with a single entry
     *
     * @return array
     */
    private function getStubCalls()
    {
        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array(200));

        $request
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response))
        ;

        return array($request);
    }
}
