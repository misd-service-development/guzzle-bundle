Changelog for 1.0.*
===================

1.0.2
-----

12 June 2013.

* Request parameters are now run through filters before trying to use the JMS Serializer.
* A `NotFoundHttpException` thrown in the param converter now includes the original Guzzle exception.
* The log format is now configurable.
* Made compatible with Guzzle 3.6.0.
* The `JMSSerializerResponseParser` now uses Guzzle's `OperationResponseParser` as a fallback rather than just the `DefaultResponseParser`.

1.0.1
-----

13 March 2013.

* Fix Symfony version dependency.
* Fix the profiler stopwatch from trying to be stopped when it's not running.
* Monolog is now optional.
* The filesystem cache path is made configurable.
* The serializer can now (de)serialize arrays of objects.

1.0.0
-----

1 February 2013.

* Initial release.
