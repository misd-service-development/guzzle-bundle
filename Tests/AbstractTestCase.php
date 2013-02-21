<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests;

use Misd\GuzzleBundle\MisdGuzzleBundle;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
use Symfony\Component\HttpKernel\KernelInterface;

class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    protected function getContainer(array $config = array(), KernelInterface $kernel = null)
    {
        if (null === $kernel) {
            $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
            $kernel
                ->expects($this->any())
                ->method('getBundles')
                ->will($this->returnValue(array()));
        }

        $bundle = new MisdGuzzleBundle($kernel);
        $extension = $bundle->getContainerExtension();

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir() . '/guzzle');
        $container->setParameter('kernel.bundles', array());
        $container->setParameter('kernel.root_dir', __DIR__ . '/Fixtures');
        $container->set('service_container', $container);
        $container->set('monolog.logger', $this->getMock('Symfony\\Bridge\\Monolog\\Logger', array(), array('app')));
        $container->set('jms_serializer', $this->getMock('JMS\\Serializer\\SerializerInterface'));

        $container->registerExtension($extension);
        $extension->load($config, $container);
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(
            array(new ResolveParameterPlaceHoldersPass(), new ResolveDefinitionTemplatesPass())
        );
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
