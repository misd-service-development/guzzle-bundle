<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Set up the MisdGuzzleBundle.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class MisdGuzzleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('plugin.xml');
        $loader->load('log.xml');
        $loader->load('cache.xml');

        if (interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            // choose a ParamConverterInterface implementation that is compatible
            // with the version of SensioFrameworkExtraBundle being used
            $parameter = new \ReflectionParameter(
                array(
                    'Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface',
                    'supports',
                ),
                'configuration'
            );
            if ('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter' == $parameter->getClass()->getName()) {
                $container->setParameter(
                    'misd_guzzle.param_converter.class',
                    'Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter3x'
                );
            } else {
                $container->setParameter(
                    'misd_guzzle.param_converter.class',
                    'Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter2x'
                );
            }
            $loader->load('param_converter.xml');
        }

        $container->setParameter(
            'guzzle.service_builder.class',
            $config['service_builder']['class']
        );
        $container->setParameter(
            'guzzle.service_builder.configuration_file',
            $config['service_builder']['configuration_file']
        );
        $container->setParameter('misd_guzzle.cache.filesystem.path', $config['filesystem_cache']['path']);

        $logFormat = $config['log']['format'];
        if (in_array($logFormat, array('default', 'debug', 'short'))) {
            $logFormat = constant(sprintf('Guzzle\Log\MessageFormatter::%s_FORMAT', strtoupper($logFormat)));
        }
        $container->setParameter('misd_guzzle.log.format', $logFormat);
        $container->setParameter('misd_guzzle.log.enabled', $config['log']['enabled']);
    }
}
