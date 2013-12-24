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

use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Log\ArrayLogAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class GuzzleDataCollector extends DataCollector
{
    protected $logAdapter;

    public function __construct(ArrayLogAdapter $logAdapter)
    {
        $this->logAdapter = $logAdapter;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        foreach ($this->logAdapter->getLogs() as $log) {
            $requestId = spl_object_hash($log['extras']['request']);

            if (isset($this->data['requests'][$requestId])) {
                continue;
            }

            $datum['message'] = $log['message'];
            $datum['time'] = $this->getRequestTime($log['extras']['response']);
            $datum['request'] = (string) $log['extras']['request'];
            $datum['response'] = (string) $log['extras']['response'];
            $datum['is_error'] = $log['extras']['response']->isError();

            $this->data['requests'][$requestId] = $datum;
        }
    }

    private function getRequestTime(GuzzleResponse $response)
    {
        $time = $response->getInfo('total_time');

        if (null === $time) {
            $time = 0;
        }

        return (int) ($time * 1000);
    }

    public function getRequests()
    {
        return $this->data['requests'];
    }

    public function getName()
    {
        return 'guzzle';
    }
}
