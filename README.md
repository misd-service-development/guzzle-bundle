GuzzleBundle
============

Integrates [Guzzle](http://guzzlephp.org/) into your Symfony2 application. It is currently under development.

Authors
-------

* Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>

Installation
------------

 1. Add GuzzleBundle to your dependencies:

        // composer.json

        {
           // ...
           "require": {
               // ...
               "misd/guzzle-bundle": "dev-master"
           }
        }

 2. Use Composer to download and install GuzzleBundle:

        $ php composer.phar update misd/guzzle-bundle

 3. Register the bundle in your application:

        // app/AppKernel.php

        class AppKernel extends Kernel
        {
            // ...
            public function registerBundles()
            {
                $bundles = array(
                    // ...
                    new Misd\GuzzleBundle\MisdGuzzleBundle(),
                    // ...
                );
            }
            // ...
        }

Usage
-----

### Clients as services

The best way to use Guzzle in Symfony2 is to let the service container create your client objects for you. Create a service for each client, and tag it with `guzzle.client`. If you aren't using a concrete client class, you can use the default Guzzle client through the `%guzzle.client.class%` parameter:

    <service id="example.client" class="%guzzle.client.class%">
        <tag name="guzzle.client"/>
        <argument>http://api.example.com/</argument>
    </service>

Using the service container allows you to easily set up your client. You can add a collection of settings in the second argument (see the Guzzle docs for details), and you can call methods to attach plugins (which have been made services themselves) etc. For example:

    <service id="example.client" class="%guzzle.client.class%">
        <tag name="guzzle.client"/>
        <argument>http://api.example.com/</argument>
        <argument type="collection">
            <argument key="setting1">true</argument>
            <argument key="setting2">false</argument>
        </argument>
        <call method="addSubscriber">
            <argument type="service" id="some_guzzle_plugin"/>
        </call>
        <call method="setUserAgent">
            <argument>My client using Guzzle</argument>
        </call>
    </service>

#### Generic client

A generic Guzzle client is available in the `guzzle.client` service:

    $client = $this->get('guzzle.client');

#### Service descriptions

To use a service description, first create a service for it:

    <service id="my.client.service_description"
             class="%guzzle.service_description.class%"
             factory-class="%guzzle.service_description.class%"
             factory-method="factory">
        <argument>%path.to.my.service_description.file%</argument>
    </service>

Set the parameter to the service description file:

    // MyBundle/DependencyInjection/MyBundleExtension.php

    $container->setParameter('path.to.my.service_description.file', __DIR__ . '/../Resources/config/client.json');

Then add the service description to your client:

    <call method="setDescription">
        <argument type="service" id="my.client.service_description"/>
    </call>

#### Caching

The bundle provides the `misd_guzzle.cache.filesystem` service, which allows you to quickly take advantage of caching (by storing files in your `app/cache` folder). Simply add the service to your client:

    <call method="addSubscriber">
        <argument type="service" id="misd_guzzle.cache.filesystem"/>
    </call>

This will be slower than using, say, Memcache, but doesn't require any dependencies.

### Guzzle service builder

The bundle also allows clients to be created through the Guzzle service builder. Add a reference to your service builder data file in your application's config file (default values shown):

    // app/config.yml

    misd_guzzle:
        service_builder:
            class:              "Guzzle\Service\Builder\ServiceBuilder"
            configuration_file: "%kernel.root_dir%/config/webservices.json"

Clients created through the Guzzle service builder can be accessed through the `guzzle.service_builder` service:

    $client = $this->get('guzzle.service_builder')->get('my_client');