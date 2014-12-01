Changelog for 1.1.*
===================

1.1.5
-----

1 December 2014.

* Fix compatibility with the Symfony 2.6+ profiler when not making any requests.
* Allow non-service clients to be tagged when using the param converter.
* Add error response plugin class name parameter.

1.1.4
-----

17 February 2014.

* Allow configuration of JMS Serializer options through the service definition's `data` property.
* Remove the need to define the client when setting the command for the ParamConverter if there is only one.
* Embedded profiler images and removed the public folder.
* Trigger Guzzle deprecation warnings in Symfony debug mode.
* Tidied up the configuration, allowing parts to be disabled and marking internal services as private.
* Stop the profiler timeline from erroneously grouping requests.

1.1.3
-----

6 January 2014.

* Fixed bug that prevented installation when the SensioFrameworkExtraBundle wasn't installed.

1.1.2
-----

24 December 2013.

* No longer has a hard dependency on the SensioFrameworkExtraBundle.
* Made compatible with both the SensioFrameworkExtraBundle 2.x and 3.x.
* Allow logging to be disabled.
* Show requests separately in the profiler timeline.
* Prevent requests from appearing more than once on the profiler page.
* Add time details to the profiler page.

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
