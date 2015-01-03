<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\DataCollector;

use Guzzle\Plugin\History\HistoryPlugin;

use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;

/**
 * GuzzleDataCollector.
 *
 * @author Ludovic Fleury <ludo.fleury@gmail.com>
 */
class GuzzleDataCollector extends DataCollector
{
    private $profiler;

    public function __construct(HistoryPlugin $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $data = array(
            'calls'       => array(),
            'error_count' => 0,
            'methods'     => array(),
            'total_time'  => 0,
        );

        /**
         * Aggregates global metrics about Guzzle usage
         *
         * @param array $request
         * @param array $response
         * @param array $time
         * @param bool  $error
         */
        $aggregate = function ($request, $response, $time, $error) use (&$data) {

            $method = $request['method'];
            if (!isset($data['methods'][$method])) {
                $data['methods'][$method] = 0;
            }

            $data['methods'][$method]++;
            $data['total_time'] += $time['total'];
            $data['error_count'] += (int) $error;
        };

        foreach ($this->profiler as $call) {
            $request = $this->collectRequest($call);
            $response = $this->collectResponse($call);
            $time = $this->collectTime($call);
            $error = $call->getResponse()->isError();

            $aggregate($request, $response, $time, $error);

            $data['calls'][] = array(
                'request' => $request,
                'response' => $response,
                'time' => $time,
                'error' => $error
            );
        }

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return isset($this->data['calls']) ? $this->data['calls'] : array();
    }

    /**
     * @return int
     */
    public function countErrors()
    {
        return isset($this->data['error_count']) ? $this->data['error_count'] : 0;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return isset($this->data['methods']) ? $this->data['methods'] : array();
    }

    /**
     * @return int
     */
    public function getTotalTime()
    {
        return isset($this->data['total_time']) ? $this->data['total_time'] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle';
    }

    /**
     * Collect & sanitize data about a Guzzle request
     *
     * @param Guzzle\Http\Message\RequestInterface $request
     *
     * @return array
     */
    private function collectRequest(GuzzleRequestInterface $request)
    {
        $body = null;
        $postFields = null;
        if ($request instanceof EntityEnclosingRequestInterface) {
            $body = (string) $request->getBody();
            $postFields = $request->getPostFields();
        }

        return array(
            'headers'    => $request->getHeaders(),
            'method'     => $request->getMethod(),
            'scheme'     => $request->getScheme(),
            'host'       => $request->getHost(),
            'path'       => $request->getPath(),
            'query'      => $request->getQuery(),
            'postFields' => $postFields,
            'body'       => $body
        );
    }

    /**
     * Collect & sanitize data about a Guzzle response
     *
     * @param Guzzle\Http\Message\RequestInterface $request
     *
     * @return array
     */
    private function collectResponse(GuzzleRequestInterface $request)
    {
        $response = $request->getResponse();
        $body = $response->getBody(true);

        return array(
            'statusCode'   => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'headers'      => $response->getHeaders(),
            'body'         => $body
        );
    }

    /**
     * Collect time for a Guzzle request
     *
     * @param Guzzle\Http\Message\RequestInterface $request
     *
     * @return array
     */
    private function collectTime(GuzzleRequestInterface $request)
    {
        $response = $request->getResponse();

        return array(
            'total'      => $response->getInfo('total_time'),
            'connection' => $response->getInfo('connect_time')
        );
    }
}
