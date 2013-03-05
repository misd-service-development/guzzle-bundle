Setting up the bundle
=====================

 1. Add GuzzleBundle to your dependencies:

        // composer.json

        {
           // ...
           "require": {
               // ...
               "misd/guzzle-bundle": "~1.0"
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
                    new Misd\GuzzleBundle\MisdGuzzleBundle()
                );
            }
        }
