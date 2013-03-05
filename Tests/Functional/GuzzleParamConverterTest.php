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

use Guzzle\Http\Message\Response;
use Guzzle\Service\Client;
use Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

class GuzzleParamConverterTest extends TestCase
{
    /**
     * @var GuzzleParamConverter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new GuzzleParamConverter();

        $this->converter->registerClient('without.description', new Client());
        $this->converter->registerClient('with.description', self::getClient('JMSSerializerBundle'));
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

    public function testApply()
    {
        $request = new Request();
        $request->attributes->set('_route_params', array('id' => 1));
        $config = $this->createConfiguration('Misd\GuzzleBundle\Tests\Fixtures\Person', array(), 'arg');

        self::$mock->addResponse(self::response200());

        $this->converter->apply($request, $config);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $request->attributes->get('arg'));
    }

    public function testApplyWithMappingAndExclude()
    {
        $request = new Request();

        $request->attributes->set('_route_params', array('real-id' => 2, 'id' => 1));
        $config = $this->createConfiguration(
            'Misd\GuzzleBundle\Tests\Fixtures\Person',
            array('mapping' => array('real-id' => 'id'), 'exclude' => array('id')),
            'arg'
        );

        self::$mock->addResponse(self::response200(2));

        $this->converter->apply($request, $config);

        $this->assertInstanceOf('Misd\GuzzleBundle\Tests\Fixtures\Person', $request->attributes->get('arg'));
        $this->assertEquals(2, $request->attributes->get('arg')->id);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testApplyWith404()
    {
        $request = new Request();
        $request->attributes->set('_route_params', array('id' => 1));
        $config = $this->createConfiguration('Misd\GuzzleBundle\Tests\Fixtures\Person', array(), 'arg');

        self::$mock->addResponse(self::response404());

        $this->converter->apply($request, $config);
    }

    public function testApplyWith404WhenOptional()
    {
        $request = new Request();
        $request->attributes->set('_route_params', array('id' => 1));
        $config = $this->createConfiguration('Misd\GuzzleBundle\Tests\Fixtures\Person', array(), 'arg', true);

        self::$mock->addResponse(self::response404());

        $this->converter->apply($request, $config);

        $this->assertNull($request->attributes->get('arg'));
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ServerErrorResponseException
     */
    public function testApplyWith500()
    {
        $request = new Request();
        $request->attributes->set('_route_params', array('id' => 1));
        $config = $this->createConfiguration('Misd\GuzzleBundle\Tests\Fixtures\Person', array(), 'arg');

        self::$mock->addResponse(self::response500());

        $this->converter->apply($request, $config);
    }

    /**
     * @param null   $class
     * @param array  $options
     * @param string $name
     * @param bool   $isOptional
     *
     * @return ConfigurationInterface
     */
    protected function createConfiguration($class = null, array $options = null, $name = 'arg', $isOptional = false)
    {
        $config = $this->getMock(
            'Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface',
            array(
                'getClass',
                'getAliasName',
                'getOptions',
                'getName',
                'isOptional',
                'allowArray',
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
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $config->expects($this->any())
            ->method('isOptional')
            ->will($this->returnValue($isOptional));

        return $config;
    }

    protected function response200($id = 1)
    {
        return Response::fromMessage(
            'HTTP/1.1 200 OK
Date: Wed, 25 Nov 2009 12:00:00 GMT
Connection: close
Server: Test
Content-Type: application/xml

<?xml version="1.0" encoding="UTF-8"?>
<person id="' . $id . '">
<name>Foo</name>
<family-name>Bar</family-name>
</person>
'
        );
    }

    protected function response404()
    {
        return Response::fromMessage('HTTP/1.1 404 Not Found');
    }

    protected function response500()
    {
        return Response::fromMessage('HTTP/1.1 500 Internal Server Error');
    }
}
