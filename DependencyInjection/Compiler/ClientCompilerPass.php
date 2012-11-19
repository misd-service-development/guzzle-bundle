<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add the bundle's listeners to Guzzle clients created as services.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class ClientCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('guzzle.client') as $id => $attributes) {
            $container->getDefinition($id)
                ->addMethodCall('addSubscriber', array((new Reference('misd_guzzle.log.monolog'))))
                ->addMethodCall('addSubscriber', array((new Reference('misd_guzzle.log.array'))))
                ->addMethodCall('addSubscriber', array((new Reference('misd_guzzle.listener.request_listener'))))
                ->addMethodCall('addSubscriber', array((new Reference('misd_guzzle.listener.command_listener'))))
            ;
        }
    }
}
