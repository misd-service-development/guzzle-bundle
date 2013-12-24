Changelog for 1.1.*
===================

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
