<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Fixtures\ConcreteCommandClient\Command;

use Guzzle\Http\Message\Request;
use Guzzle\Service\Command\AbstractCommand;

class GetPerson extends AbstractCommand
{
    public function build()
    {
        $this->request = $this->client->createRequest('GET', 'http://api.example.com/api/person/1');
    }
}
