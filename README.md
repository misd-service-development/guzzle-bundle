GuzzleBundle
============

Integrates [Guzzle](http://guzzlephp.org/) into your Symfony2 application.

It also integrates with the [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle) (if installed) for easy object (de)serialization.

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

### (De)serialization

*This feature is experimental and the syntax is subject to change.*

The bundle integrates with the [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle), allowing you to easily work with concrete objects without having to create concrete commands.

The JMSSerializerBundle needs to be installed separately.

#### Responses

To turn a response into an object (ie deserialize), set the `responseClass` value in your command as a fully-qualified class name.

For example:

    "GetPerson":{
        "httpMethod":"GET",
        "uri":"person/{id}",
        "summary":"Gets a person",
        "responseClass":"Vendor\\MyBundle\\Entity\\Person",
        "parameters":{
            "id":{
                "location":"uri",
                "type":"integer",
                "description":"Person to retrieve by ID",
                "required":"true"
            }
        }
    }

Executing the `GetPerson` command will now return an instance of `Vendor\MyBundle\Entity\Person`:

    $command = $client->getCommand('GetPerson', array('id' => $id));
    $person = $client->execute($command);

#### Requests

To send a (serialized) object in your request, put your object in a `body` parameter. You should also set the `instanceOf` value in the parameter as the fully-qualified class name.

By default it will serialize the object into XML. The change this, set the `sentAs` value as a format that the JMSSerializerBundle can use (ie `json`, `yml` or `xml`).

For example:

    "CreatePerson":{
        "httpMethod":"POST",
        "uri":"person",
        "summary":"Create a person",
        "parameters":{
            "person":{
                "location":"body",
                "instanceOf":"Vendor\\MyBundle\\Entity\\Person",
                "sentAs":"json",
                "required":"true"
            }
        }
    }

Executing the `CreatePerson` command will now send an instance of `Vendor\MyBundle\Entity\Person` as JSON:

    $command = $client->getCommand('CreatePerson', array('person' => $person));
    $client->execute($command);
