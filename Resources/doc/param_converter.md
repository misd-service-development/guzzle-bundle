Param converter
===============

Clients that have been initialised through the Symfony2 service container and have their [responses deserialized by the JMSSerializerBundle](serialization.html) can make use of the bundle's [parameter converter](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html).

The syntax is based on the Doctrine param converter.

The SensioFrameworkExtraBundle needs to be installed separately.

Basic usage
-----------

The simpliest way is to just type-hint your controller method:

    /**
     * @Route("blog/{id}")
     */
    public function showAction(Post $post)

The bundle searches through your clients and finds a `GET` command that has its `responseClass` set as the required class (`Vendor\MyBundle\Post`). If it finds one, it will execute it using the route parameters (ie `{id}`).

A successful response (ie the deserialized `Vendor\MyBundle\Post`) is injected into the method arguments. If the web service returns a `404` response, a Symfony2 exception (`Symfony\Component\HttpKernel\Exception\NotFoundHttpException`) is thrown; with any other abnormal web service response, the normal Guzzle exception is thrown.

You can configure the conversion in a `@ParamConverter` annotation.

Defining the command/client
---------------------------

If you have more than one command available that returns the class that you're after, you can explictly set with client and command to use through the `client` and `command` options.

For example, to force use of the `example.client`'s `GetPost` command:

    /**
     * @Route("blog/{id}")
     * @ParamConverter("post", options={"client"="example.client", "command"="GetPost"})
     */
    public function showAction(Post $post)

Alternatively, if you just set the client it will search for a command that matches:

    /**
     * @Route("blog/{id}")
     * @ParamConverter("post", options={"client"="example.client"})
     */
    public function showAction(Post $post)

Parameter mapping
-----------------

If your route parameter does not have the same name as your command's paramater, you can use the `mapping` option.

For example, to map the route's `post_id` to the command's `id` parameter:

    /**
     * @Route("blog/{post_id}")
     * @ParamConverter("post", options={"mapping": {"id": "post_id"}})
     */
    public function showAction(Post $post)

This then allows you to have mutiple converters in one action:

    /**
     * @Route("blog/{id}/comments/{comment_id}")
     * @ParamConverter("comment", options={"mapping": {"id": "comment_id"}})
     */
    public function showAction(Post $post, Comment $comment)

If you need to exclude a route parameter from being used:

    /**
    * @Route("blog/{date}/{slug}")
    * @ParamConverter("post", options={"exclude": {"date"}})
    */
    public function showAction(Post $post, \DateTime $date)
