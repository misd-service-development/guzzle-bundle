Creating clients
================

Guzzle clients can be created in 2 ways:

1. Using the Symfony2 service container (recommended).
2. Using the Guzzle service builder.

Using the Symfony2 service container
------------------------------------

The best way to use Guzzle in Symfony2 is to let the service container create your client objects for you. Create a service for each client in your bundle, and tag it with `guzzle.client`. If you aren't using a concrete client class, you can use the default Guzzle client through the `%guzzle.client.class%` parameter:

    // MyBundle/Resources/config/services.xml

    <service id="example.client" class="%guzzle.client.class%">
        <tag name="guzzle.client"/>
        <argument>http://api.example.com/</argument>
    </service>

Using the service container allows you to easily set up your client. You can add a collection of settings in the second argument (see the Guzzle docs for details), and you can call methods to [attach plugins](plugins.md) etc. For example:

    // MyBundle/Resources/config/services.xml

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
            <argument>My Guzzle client</argument>
            <argument>true</argument>
        </call>
    </service>

You can then access your service through the container. For example, in a controller:

    $client = $this->get('example.client');

### Generic client

A generic Guzzle client is available in the `guzzle.client` service:

    $client = $this->get('guzzle.client');

You could use this to access absolute URLs:

    $request = $client->get('http://www.example.com/');

Using the Guzzle service builder
--------------------------------

The bundle also allows clients to be created through the Guzzle service builder. Add a reference to your service builder data file in your application's config file (default values shown):

    // app/config.yml

    misd_guzzle:
        service_builder:
            class:              "Guzzle\Service\Builder\ServiceBuilder"
            configuration_file: "%kernel.root_dir%/config/webservices.json"

Clients created through the Guzzle service builder can be accessed through the `guzzle.service_builder` service:

    $client = $this->get('guzzle.service_builder')->get('my_client');

As service builder clients do not have access to the Symfony2 service container, you will need to attach the Guzzle service description and any plugins directly in your client's `factory` method:

    public static function factory($config = array()) {
        // ...
        $client->setDescription(__DIR__ . '/client.json');

        $authPlugin = new \Guzzle\Plugin\CurlAuth\CurlAuthPlugin('username', 'password');
        $client->addSubscriber($authPlugin);
