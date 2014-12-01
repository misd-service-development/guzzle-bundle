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
        $plugins = $container->findTaggedServiceIds('misd_guzzle.plugin');

        foreach ($container->findTaggedServiceIds('guzzle.client') as $id => $attributes) {
            foreach ($plugins as $plugin => $pluginAttributes) {
                $container->getDefinition($id)->addMethodCall('addSubscriber', array((new Reference($plugin))));
            }
            if ('guzzle.client' !== $id && $container->hasDefinition('misd_guzzle.param_converter')) {
                $class = $container->getDefinition($id)->getClass();

                if (true === $container->hasParameter(trim($class, '%'))) {
                    $class = $container->getParameter(trim($class, '%'));
                }

                if (
                    false === class_exists($class)
                    ||
                    ($class !== 'Guzzle\Service\ClientInterface' || false === is_subclass_of($class, 'Guzzle\Service\ClientInterface'))
                ) {
                    continue;
                }

                $container->getDefinition('misd_guzzle.param_converter')
                    ->addMethodCall('registerClient', array($id, new Reference($id)))
                ;
            }
        }
    }
}
