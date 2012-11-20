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
            $log['request'] = (string) $log['extras']['request'];
            $log['response'] = (string) $log['extras']['response'];
            unset($log['extras']['handle']); // can break serialization
            $this->data['logs'][] = $log;
        }
    }

    public function getLogs()
    {
        return $this->data['logs'];
    }

    public function countLogs()
    {
        return count($this->data['logs']);
    }

    public function getName()
    {
        return 'guzzle';
    }
}
