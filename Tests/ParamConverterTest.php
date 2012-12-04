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

use Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class ParamConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GuzzleParamConverter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new GuzzleParamConverter();

        $this->converter->registerClient('without.description', new \Guzzle\Service\Client());

        $client = new \Guzzle\Service\Client();
        $client->setDescription(
            \Guzzle\Service\Description\ServiceDescription::factory(__DIR__ . '/Fixtures/config/client.json')
        );

        $this->converter->registerClient('with.description', $client);
    }

    public function testSupports()
    {
        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration('Misd\GuzzleBundle\Tests\Fixtures\Person');
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('client' => 'with.description')
        );
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('client' => 'with.description', 'command' => 'GetPersonClass')
        );
        $this->assertTrue($this->converter->supports($config));
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnknownClient()
    {
        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('client' => 'unknown.client')
        );
        $this->converter->supports($config);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCommandWithoutClient()
    {
        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('command' => 'UnknownCommand')
        );
        $this->converter->supports($config);
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnknownCommand()
    {
        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('client' => 'with.description', 'command' => 'UnknownCommand')
        );
        $this->converter->supports($config);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCommandReturnsWrongClass()
    {
        $config = $this->createConfiguration(
            __CLASS__,
            array('client' => 'with.description', 'command' => 'GetPersonClass')
        );
        $this->converter->supports($config);
    }

    public function createConfiguration($class = null, array $options = null)
    {
        $config = $this->getMock(
            'Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface',
            array(
                'getClass',
                'getAliasName',
                'getOptions',
                'getName',
            )
        );
        if ($class !== null) {
            $config->expects($this->any())
                ->method('getClass')
                ->will($this->returnValue($class));
        }
        if ($options !== null) {
            $config->expects($this->any())
                ->method('getOptions')
                ->will($this->returnValue($options));
        }

        return $config;
    }
}
