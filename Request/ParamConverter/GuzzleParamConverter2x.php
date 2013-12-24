<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * GuzzleParamConverter (this version is compatible with SensioFrameworkExtraBundle 2.x).
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class GuzzleParamConverter2x extends AbstractGuzzleParamConverter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        return $this->execute($request, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return null !== $this->find($configuration);
    }
}
