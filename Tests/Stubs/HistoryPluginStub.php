<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Stubs;

use IteratorAggregate;
use Guzzle\Plugin\History\HistoryPlugin;

/**
 * Fake History plugin
 *
 * History plugin class provide an accessor for transaction
 * Yet we still use iterator access for BC.
 *
 * @see https://github.com/guzzle/plugin-history/commit/01a37820233c781b22a94a4e7e1cb822c29bb1dd#diff-7ab0bd7dfdcd628c04405257974fb612R94
 *
 * @author Ludovic Fleury <ludo.fleury@gmail.com>
 */
class HistoryPluginStub extends HistoryPlugin implements IteratorAggregate
{
    private $stubJournal = array();

    public function __construct(array $stubJournal)
    {
        $this->stubJournal = $stubJournal;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->stubJournal);
    }
}
