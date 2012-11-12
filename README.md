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
