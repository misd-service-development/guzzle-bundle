<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\DependencyInjection;

use Guzzle\Common\Version;
use Misd\GuzzleBundle\DependencyInjection\MisdGuzzleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class MisdGuzzleExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var MisdGuzzleExtension
     */
    private $extension;

    protected function setUp()
    {
        if (!version_compare(Version::VERSION, '3.6', '>=')) {
            $this->markTestSkipped('the emitWarning property was added in Guzzle 3.6');
        }

        $this->container = new ContainerBuilder();
        $this->extension = new MisdGuzzleExtension();

        // reset the emit warnings options before each test
        Version::$emitWarnings = false;
    }

    public function testWithoutDebugParameter()
    {
        $this->extension->load(array(), $this->container);

        $this->assertFalse(Version::$emitWarnings);
    }

    public function testDebugDisabled()
    {
        $this->container->setParameter('kernel.debug', false);
        $this->extension->load(array(), $this->container);

        $this->assertFalse(Version::$emitWarnings);
    }

    public function testDebugEnabled()
    {
        $this->container->setParameter('kernel.debug', true);
        $this->extension->load(array(), $this->container);

        $this->assertTrue(Version::$emitWarnings);
    }
}
