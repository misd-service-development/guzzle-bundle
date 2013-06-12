Changelog for 1.1.*
===================

1.1.1
-----

12 June 2013.

* Request parameters are now run through filters before trying to use the JMS Serializer.
* A `NotFoundHttpException` thrown in the param converter now includes the original Guzzle exception.
* The log format is now configurable.
* Made compatible with Guzzle 3.6.0.
* The `JMSSerializerResponseParser` now uses Guzzle's `OperationResponseParser` as a fallback rather than just the `DefaultResponseParser`.

1.1.0
-----

13 March 2013.

* First 1.1.* release.
