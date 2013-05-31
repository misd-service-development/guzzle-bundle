<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\DependencyInjection;

use Guzzle\Log\MessageFormatter;
use Misd\GuzzleBundle\Tests\AbstractTestCase;

class ContainerTest extends AbstractTestCase
{
    /**
     * @dataProvider configs
     */
    public function testDefault(array $config = array())
    {
        $container = $this->getContainer(array($config));

        // core

        $this->assertTrue($container->has('guzzle.client'));
        $this->assertInstanceOf('Guzzle\Service\Client', $container->get('guzzle.client'));
        $this->assertTrue($container->getDefinition('guzzle.client')->hasTag('guzzle.client'));

        $this->assertTrue($container->hasParameter('guzzle.service_builder.class'));
        $this->assertTrue($container->has('guzzle.service_builder'));
        $this->assertInstanceOf(
            'Guzzle\Service\Builder\ServiceBuilderInterface',
            $container->get('guzzle.service_builder')
        );

        $this->assertTrue($container->has('misd_guzzle.response.parser'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\Service\Command\LocationVisitor\Request\JMSSerializerBodyVisitor',
            $container->get('misd_guzzle.request.visitor.body')
        );

        $this->assertTrue($container->has('misd_guzzle.response.parser'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\Service\Command\JMSSerializerResponseParser',
            $container->get('misd_guzzle.response.parser')
        );

        $this->assertTrue($container->has('misd_guzzle.log.array'));
        $this->assertInstanceOf('Guzzle\Plugin\Log\LogPlugin', $container->get('misd_guzzle.log.array'));
        $this->assertTrue($container->getDefinition('misd_guzzle.log.array')->hasTag('misd_guzzle.plugin'));

        $this->assertTrue($container->has('misd_guzzle.log.adapter.array'));
        $this->assertInstanceOf('Guzzle\Log\ArrayLogAdapter', $container->get('misd_guzzle.log.adapter.array'));

        $this->assertTrue($container->has('misd_guzzle.data_collector'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\DataCollector\GuzzleDataCollector',
            $container->get('misd_guzzle.data_collector')
        );
        $this->assertTrue($container->getDefinition('misd_guzzle.data_collector')->hasTag('data_collector'));

        $this->assertTrue($container->has('misd_guzzle.listener.request_listener'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\EventListener\RequestListener',
            $container->get('misd_guzzle.listener.request_listener')
        );
        $this->assertTrue(
            $container->getDefinition('misd_guzzle.listener.request_listener')->hasTag('misd_guzzle.plugin')
        );

        $this->assertTrue($container->has('misd_guzzle.listener.command_listener'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\EventListener\CommandListener',
            $container->get('misd_guzzle.listener.command_listener')
        );
        $this->assertTrue(
            $container->getDefinition('misd_guzzle.listener.command_listener')->hasTag('misd_guzzle.plugin')
        );

        $this->assertTrue($container->has('misd_guzzle.param_converter'));
        $this->assertInstanceOf(
            'Misd\GuzzleBundle\Request\ParamConverter\GuzzleParamConverter',
            $container->get('misd_guzzle.param_converter')
        );

        // monolog

        $this->assertTrue($container->has('misd_guzzle.log.monolog'));
        $this->assertInstanceOf('Guzzle\Plugin\Log\LogPlugin', $container->get('misd_guzzle.log.monolog'));
        $this->assertTrue($container->getDefinition('misd_guzzle.log.monolog')->hasTag('misd_guzzle.plugin'));
        $this->assertEquals(MessageFormatter::DEFAULT_FORMAT, $container->getDefinition('misd_guzzle.log.monolog')->getArgument(1));

        $this->assertTrue($container->has('misd_guzzle.log.adapter.monolog'));
        $this->assertInstanceOf('Guzzle\Log\MonologLogAdapter', $container->get('misd_guzzle.log.adapter.monolog'));
        $this->assertTrue($container->getDefinition('misd_guzzle.log.adapter.monolog')->hasTag('monolog.logger'));

        // plugin

        $this->assertTrue($container->hasParameter('guzzle.plugin.async.class'));
        $this->assertEquals('Guzzle\Plugin\Async\AsyncPlugin', $container->getParameter('guzzle.plugin.async.class'));

        $this->assertTrue($container->hasParameter('guzzle.plugin.backoff.class'));
        $this->assertEquals(
            'Guzzle\Plugin\Backoff\BackoffPlugin',
            $container->getParameter('guzzle.plugin.backoff.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.cache.class'));
        $this->assertEquals('Guzzle\Plugin\Cache\CachePlugin', $container->getParameter('guzzle.plugin.cache.class'));

        $this->assertTrue($container->hasParameter('guzzle.plugin.cookie.class'));
        $this->assertEquals(
            'Guzzle\Plugin\Cookie\CookiePlugin',
            $container->getParameter('guzzle.plugin.cookie.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.curl_auth.class'));
        $this->assertEquals(
            'Guzzle\Plugin\CurlAuth\CurlAuthPlugin',
            $container->getParameter('guzzle.plugin.curl_auth.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.history.class'));
        $this->assertEquals(
            'Guzzle\Plugin\History\HistoryPlugin',
            $container->getParameter('guzzle.plugin.history.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.log.class'));
        $this->assertEquals('Guzzle\Plugin\Log\LogPlugin', $container->getParameter('guzzle.plugin.log.class'));

        $this->assertTrue($container->hasParameter('guzzle.plugin.md5_validator.class'));
        $this->assertEquals(
            'Guzzle\Plugin\Md5\Md5ValidatorPlugin',
            $container->getParameter('guzzle.plugin.md5_validator.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.command_content_md5.class'));
        $this->assertEquals(
            'Guzzle\Plugin\Md5\CommandContentMd5Plugin',
            $container->getParameter('guzzle.plugin.command_content_md5.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.plugin.mock.class'));
        $this->assertEquals('Guzzle\Plugin\Mock\MockPlugin', $container->getParameter('guzzle.plugin.mock.class'));

        $this->assertTrue($container->hasParameter('guzzle.plugin.oauth.class'));
        $this->assertEquals('Guzzle\Plugin\Oauth\OauthPlugin', $container->getParameter('guzzle.plugin.oauth.class'));

        // log

        $this->assertTrue($container->hasParameter('guzzle.log.adapter.monolog.class'));
        $this->assertEquals(
            'Guzzle\Log\MonologLogAdapter',
            $container->getParameter('guzzle.log.adapter.monolog.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.log.adapter.array.class'));
        $this->assertEquals('Guzzle\Log\ArrayLogAdapter', $container->getParameter('guzzle.log.adapter.array.class'));

        // cache

        $this->assertTrue($container->hasParameter('guzzle.cache.doctrine.class'));
        $this->assertEquals(
            'Guzzle\Cache\DoctrineCacheAdapter',
            $container->getParameter('guzzle.cache.doctrine.class')
        );

        $this->assertTrue($container->hasParameter('guzzle.cache.doctrine.filesystem.class'));
        $this->assertEquals(
            'Doctrine\Common\Cache\FilesystemCache',
            $container->getParameter('guzzle.cache.doctrine.filesystem.class')
        );

        $this->assertTrue($container->has('misd_guzzle.cache.doctrine.filesystem.adapter'));
        $this->assertInstanceOf(
            'Doctrine\Common\Cache\FilesystemCache',
            $container->get('misd_guzzle.cache.doctrine.filesystem.adapter')
        );

        $this->assertTrue($container->has('misd_guzzle.cache.doctrine.filesystem'));
        $this->assertInstanceOf(
            'Guzzle\Cache\DoctrineCacheAdapter',
            $container->get('misd_guzzle.cache.doctrine.filesystem')
        );

        $this->assertTrue($container->has('misd_guzzle.cache.filesystem'));
        $this->assertInstanceOf('Guzzle\Plugin\Cache\CachePlugin', $container->get('misd_guzzle.cache.filesystem'));
    }

    public function configs()
    {
        $configs = array();

        $configs[] = array();
        $configs[] = array('guzzle' => array('service_builder' => array('class' => 'Misd\GuzzleBundle\Tests\Fixtures\ServiceBuilder')));
        $configs[] = array('guzzle' => array('service_builder' => array('configuration_file' => '%kernel.root_dir%/config/alt-webservices.json')));

        return $configs;
    }

    /**
     * @dataProvider logFormats
     */
    public function testLogFormat($parameter, $format = null)
    {
        $container = $this->getContainer(array(array('log' => array('format' => $parameter))));
        $this->assertEquals($format ?: $parameter, $container->getDefinition('misd_guzzle.log.monolog')->getArgument(1));
    }

    public function logFormats()
    {
        return array(
            array('debug', MessageFormatter::DEBUG_FORMAT),
            array('short', MessageFormatter::SHORT_FORMAT),
            array('foo bar baz')
        );
    }
}
