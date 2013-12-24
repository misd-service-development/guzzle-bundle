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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * GuzzleParamConverter (this version is compatible with SensioFrameworkExtraBundle 3.x).
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class GuzzleParamConverter3x extends AbstractGuzzleParamConverter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->execute($request, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return null !== $this->find($configuration);
    }
}
