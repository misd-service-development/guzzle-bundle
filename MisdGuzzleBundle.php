<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Misd\GuzzleBundle\DependencyInjection\Compiler\ClientCompilerPass;
use Misd\GuzzleBundle\DependencyInjection\Compiler\MonologCompilerPass;
use Misd\GuzzleBundle\DependencyInjection\Compiler\ServiceBuilderCompilerPass;

/**
 * MisdGuzzleBundle integrates {@link http://guzzlephp.org/ Guzzle} into your
 * Symfony2 application.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class MisdGuzzleBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MonologCompilerPass());
        $container->addCompilerPass(new ClientCompilerPass());
        $container->addCompilerPass(new ServiceBuilderCompilerPass());
    }
}
