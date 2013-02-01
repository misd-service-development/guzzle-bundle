<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Fixtures\ConcreteCommandClient;

use Guzzle\Service\Client;

class ConcreteCommandClient extends Client
{
    public static function factory($config = array())
    {
        $config['base_url'] = 'http://api.example.com'; // for Guzzle <= 3.0.1

        return parent::factory($config);
    }
}
