<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\DataCollector;

use Misd\GuzzleBundle\DataCollector\GuzzleDataCollector;
use Misd\GuzzleBundle\Tests\Stubs\HistoryPluginStub;

/**
 * Guzzle DataCollector unit test
 *
 * @author Ludovic Fleury <ludo.fleury@gmail.com>
 */
class GuzzleDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $guzzleDataCollector = $this->createGuzzleCollector();

        $this->assertEquals($guzzleDataCollector->getName(), 'guzzle');
    }

    /**
     * Test an empty GuzzleDataCollector
     */
    public function testCollectEmpty()
    {
        // test an empty collector
        $guzzleDataCollector = $this->createGuzzleCollector();

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $guzzleDataCollector->collect($request, $response);

        $this->assertEquals($guzzleDataCollector->getCalls(), array());
        $this->assertEquals($guzzleDataCollector->countErrors(), 0);
        $this->assertEquals($guzzleDataCollector->getMethods(), array());
        $this->assertEquals($guzzleDataCollector->getTotalTime(), 0);
    }

    /**
     * Test a DataCollector containing one valid call
     *
     * HTTP response code 100+ and 200+
     */
    public function testCollectValidCall()
    {
        // test a regular call
        $callInfos = array('connect_time' => 15, 'total_time' => 150);
        $callUrlQuery = $this->stubQuery(array('foo' => 'bar'));
        $callRequest = $this->stubRequest('get', 'http', 'test.local', '/', $callUrlQuery);
        $callResponse = $this->stubResponse(200, 'OK', 'Hello world');
        $call = $this->stubCall($callRequest, $callResponse, $callInfos);
        $guzzleDataCollector = $this->createGuzzleCollector(array($call));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $guzzleDataCollector->collect($request, $response);

        $this->assertEquals(count($guzzleDataCollector->getCalls()), 1);
        $this->assertEquals($guzzleDataCollector->countErrors(), 0);
        $this->assertEquals($guzzleDataCollector->getMethods(), array('get' => 1));
        $this->assertEquals($guzzleDataCollector->getTotalTime(), 150);

        $calls = $guzzleDataCollector->getCalls();
        $this->assertEquals(
            $calls[0],
            array(
                'request' => array(
                    'headers' => null,
                    'method'  => 'get',
                    'scheme'  => 'http',
                    'host'    => 'test.local',
                    'path'    => '/',
                    'query'   => $callUrlQuery,
                    'body'    => null
                ),
                'response' => array(
                    'statusCode'   => 200,
                    'reasonPhrase' => 'OK',
                    'headers'      => null,
                    'body'         => 'Hello world',
                ),
                'time' => array(
                    'total'      => 150,
                    'connection' => 15
                ),
                'error' => false
            )
        );
    }

    /**
     * Test a DataCollector containing one faulty call
     *
     * HTTP response code 400+ & 500+
     */
    public function testCollectErrorCall()
    {
        // test an error call
        $callInfos = array('connect_time' => 15, 'total_time' => 150);
        $callUrlQuery = $this->stubQuery(array('foo' => 'bar'));
        $callRequest = $this->stubRequest('post', 'http', 'test.local', '/', $callUrlQuery);
        $callResponse = $this->stubResponse(404, 'Not found', 'Oops');
        $call = $this->stubCall($callRequest, $callResponse, $callInfos);
        $guzzleDataCollector = $this->createGuzzleCollector(array($call));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $guzzleDataCollector->collect($request, $response);

        $this->assertEquals(count($guzzleDataCollector->getCalls()), 1);
        $this->assertEquals($guzzleDataCollector->countErrors(), 1);
        $this->assertEquals($guzzleDataCollector->getMethods(), array('post' => 1));
        $this->assertEquals($guzzleDataCollector->getTotalTime(), 150);

        $calls = $guzzleDataCollector->getCalls();
        $this->assertEquals(
            $calls[0],
            array(
                'request' => array(
                    'headers' => null,
                    'method'  => 'post',
                    'scheme'  => 'http',
                    'host'    => 'test.local',
                    'path'    => '/',
                    'query'   => $callUrlQuery,
                    'body'    => null,
                ),
                'response' => array(
                    'statusCode'   => 404,
                    'reasonPhrase' => 'Not found',
                    'headers'      => null,
                    'body'         => 'Oops',
                ),
                'time' => array(
                    'total'      => 150,
                    'connection' => 15
                ),
                'error' => true
            )
        );
    }

    /**
     * Test a DataCollector containing one call with request content
     *
     * The request has a body content like POST or PUT
     * In this case the call contains a Guzzle\Http\Message\EntityEnclosingRequestInterface
     * which should be sanitized/casted as a string
     */
    public function testCollectBodyRequestCall()
    {
        $callBody = $this->getMock('Guzzle\Stream\StreamInterface');
        $callBody
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('Request body string'))
        ;
        $callInfos = array('connect_time' => 15, 'total_time' => 150);
        $callUrlQuery = $this->stubQuery(array('foo' => 'bar'));
        $callRequest = $this->stubRequest('post', 'http', 'test.local', '/', $callUrlQuery, $callBody);
        $callResponse = $this->stubResponse(201, 'Created', '');
        $call = $this->stubCall($callRequest, $callResponse, $callInfos);
        $guzzleDataCollector = $this->createGuzzleCollector(array($call));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $guzzleDataCollector->collect($request, $response);

        $this->assertEquals(count($guzzleDataCollector->getCalls()), 1);
        $this->assertEquals($guzzleDataCollector->countErrors(), 0);
        $this->assertEquals($guzzleDataCollector->getMethods(), array('post' => 1));
        $this->assertEquals($guzzleDataCollector->getTotalTime(), 150);

        $calls = $guzzleDataCollector->getCalls();
        $this->assertEquals(
            $calls[0],
            array(
                'request' => array(
                    'headers' => null,
                    'method'  => 'post',
                    'scheme'  => 'http',
                    'host'    => 'test.local',
                    'path'    => '/',
                    'query'   => $callUrlQuery,
                    'body'    => 'Request body string',
                ),
                'response' => array(
                    'statusCode'   => 201,
                    'reasonPhrase' => 'Created',
                    'headers'      => null,
                    'body'         => '',
                ),
                'time' => array(
                    'total'      => 150,
                    'connection' => 15
                ),
                'error' => false
            )
        );

    }

    /**
     * Create the DataCollector
     *
     * @param array $calls An array of calls
     *
     * @return Misd\GuzzleBundle\DataCollector\GuzzleDataCollector
     */
    protected function createGuzzleCollector(array $calls = array())
    {
        return new GuzzleDataCollector(new HistoryPluginStub($calls));
    }

    /**
     * Stub a Guzzle call (processed request)
     *
     * @param Guzzle\Http\Message\RequestInterface $request
     * @param Guzzle\Http\Message\Response         $response
     * @param array                                $infos    call informations
     *
     * @return Guzzle\Http\Message\RequestInterface
     */
    protected function stubCall($request, $response, array $infos)
    {
        $request
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response))
        ;

        $response
            ->expects($this->any())
            ->method('getInfo')
            ->with(
                $this->logicalOr(
                    $this->equalTo('connect_time'),
                    $this->equalTo('total_time')
                )
            )
            ->will(
                $this->returnCallback(
                    function ($arg) use ($infos) {
                        if (!isset($infos[$arg])) {
                            throw new Exception(sprintf('%s is not a mocked information', $arg));
                        }

                        return $infos[$arg];
                    }
                )
            )
        ;

        return $request;
    }

    /**
     * Stub a Guzzle QueryString
     *
     * @param array $query Array of url query parameters
     *
     * @return Guzzle\Http\QueryString
     */
    protected function stubQuery(array $query)
    {
        $query = $this->getMock('Guzzle\Http\QueryString');
        $query
            ->expects($this->any())
            ->method('__toString()')
            ->will($this->returnValue(http_build_query($query)))
        ;

        $query
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($query))
        ;

        return $query;
    }

    /**
     * Stub a Guzzle request
     *
     * @param string                        $method get, post
     * @param string                        $scheme http, https
     * @param string                        $host   test.tld
     * @param string                        $path   /test
     * @param Guzzle\Http\QueryString       $query
     * @param Guzzle\Stream\StreamInterface $body
     *
     * @return Guzzle\Http\Message\RequestInterface
     */
    protected function stubRequest($method, $scheme, $host, $path, $query, $body = null)
    {
        $mockClassName = null === $body ? 'RequestInterface' : 'EntityEnclosingRequestInterface';
        $request = $this->getMock(sprintf('Guzzle\Http\Message\%s', $mockClassName));
        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method))
        ;

        $request
            ->expects($this->any())
            ->method('getScheme')
            ->will($this->returnValue($scheme))
        ;

        $request
            ->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue($host))
        ;

        $request
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path))
        ;

        $request
            ->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query))
        ;

        if (null !== $body) {
            $request
                ->expects($this->any())
                ->method('getBody')
                ->will($this->returnValue($body))
            ;
        }

        return $request;
    }

    /**
     * Stub a Guzzle response
     *
     * @param int    $code
     * @param string $reason
     * @param string $body
     *
     * @return Guzzle\Http\Message\Response
     */
    protected function stubResponse($code, $reason, $body)
    {
        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array($code));
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($code))
        ;

        $response
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue($reason))
        ;

        $response
            ->expects($this->any())
            ->method('getBody')
            ->with($this->equalTo(true))
            ->will($this->returnValue($body))
        ;

        $response
            ->expects($this->any())
            ->method('isError')
            ->will($this->returnValue($code > 399 && $code < 600))
        ;

        return $response;
    }
}
