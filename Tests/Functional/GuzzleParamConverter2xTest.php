<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Functional;

use Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter2x;

class GuzzleParamConverter2xTest extends AbstractGuzzleParamConverterTest
{
    public function setUp()
    {
        // skip the test if the installed version of SensioFrameworkExtraBundle
        // is not compatible with the GuzzleParamConverter2x class
        $parameter = new \ReflectionParameter(
            array(
                'Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface',
                'supports',
            ),
            'configuration'
        );
        if ('Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface' != $parameter->getClass()->getName()) {
            $this->markTestSkipped(
                'skipping GuzzleParamConverter2xTest due to an incompatible version of the SensioFrameworkExtraBundle'
            );
        }

        $this->converter = new GuzzleParamConverter2x();

        parent::setUp();
    }
}
