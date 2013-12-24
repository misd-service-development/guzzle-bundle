Service descriptions
====================

> Clients created through the Guzzle service builder need to attach a service description in their `factory()` method instead.

To use a Guzzle service description, first create a service for it:

    // MyBundle/Resources/config/services.xml

    <service id="example.client.service_description"
             class="%guzzle.service_description.class%"
             factory-class="%guzzle.service_description.class%"
             factory-method="factory">
        <argument>%path.to.my.service_description.file%</argument>
    </service>

Next set the parameter for the service description file location:

    // MyBundle/DependencyInjection/MyBundleExtension.php

    $container->setParameter('path.to.my.service_description.file', __DIR__ . '/../Resources/config/client.json');

Then add the service description to your client through the `setDescription()` method:

    // MyBundle/Resources/config/services.xml

    <service id="example.client" class="%guzzle.client.class%">
        // ...
        <call method="setDescription">
            <argument type="service" id="example.client.service_description"/>
        </call>
    </service>
