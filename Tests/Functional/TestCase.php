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

use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected static function getTestCases()
    {
        return array('Basic', 'JMSSerializerBundle');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        foreach (self::getTestCases() as $testCase) {
            self::$kernels[$testCase] = self::createKernel(array('test_case' => $testCase));
        }

        self::$mock = new MockPlugin();

        foreach (self::getTestCases() as $testCase) {
            $client = self::getContainer($testCase)->get('guzzle.client');
            $client->setDescription(ServiceDescription::factory(__DIR__ . '/../Fixtures/config/client.json'));
            $client->addSubscriber(self::$mock);
            self::$clients[$testCase] = $client;
        }
    }

    public static function tearDownAfterClass()
    {
        foreach (self::getTestCases() as $testCase) {
            self::deleteTmpDir($testCase);
        }

        parent::tearDownAfterClass();
    }

    /**
     * @var Kernel[]
     */
    private static $kernels = array();

    protected static function getKernel($testCase)
    {
        if (false === isset(self::$kernels[$testCase])) {
            throw new InvalidArgumentException(sprintf('Unknown testCase %s', $testCase));
        }

        return self::$kernels[$testCase];
    }

    protected static function getContainer($testCase)
    {
        if (false === isset(self::$kernels[$testCase])) {
            throw new InvalidArgumentException(sprintf('Unknown testCase %s', $testCase));
        }

        return self::$kernels[$testCase]->getContainer();
    }

    private static function deleteTmpDir($testCase)
    {
        if (!file_exists($dir = sys_get_temp_dir() . '/' . Kernel::VERSION . '/' . $testCase)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    private static function createKernel(array $options = array())
    {
        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        self::deleteTmpDir($options['test_case']);

        $kernel = new \Misd\GuzzleBundle\Tests\Functional\app\AppKernel(
            $options['test_case'],
            isset($options['root_config']) ? $options['root_config'] : 'config.yml',
            isset($options['environment']) ? $options['environment'] : 'guzzlebundletest',
            isset($options['debug']) ? $options['debug'] : true
        );

        $kernel->boot();

        return $kernel;
    }

    /**
     * @var Client[]
     */
    protected static $clients = array();
    /**
     * @var MockPlugin
     */
    protected static $mock;

    protected static function getClient($testCase)
    {
        if (false === isset(self::$clients[$testCase])) {
            throw new InvalidArgumentException(sprintf('Unknown testCase %s', $testCase));
        }

        return self::$clients[$testCase];
    }

}
