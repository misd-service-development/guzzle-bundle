Object (de)serialization
========================

*This feature is experimental and the syntax is subject to change.*

The bundle integrates with the [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle), allowing you to easily work with concrete objects without having to create concrete commands.

The JMSSerializerBundle needs to be installed separately.

Responses
---------

To turn a response into an object (ie deserialize it), set the `responseClass` value in your command as a fully-qualified class name.

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

### Arrays

The `responseClass` value can actually be any of the JMS Serializer's `@Type` annotation value forms which contain a classname.

For example, to deserialize a JSON array that doesn't have a root element:

    "GetPeople":{
        "httpMethod":"GET",
        "uri":"people",
        "summary":"Gets an array of people",
        "responseClass":"array<Vendor\\MyBundle\\Entity\\Person>"
    }

Executing the `GetPeople` command will now return an array of `Vendor\MyBundle\Entity\Person` instances:

    $command = $client->getCommand('GetPeople');
    $people = $client->execute($command);

Requests
--------

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
                "type":"object",
                "instanceOf":"Vendor\\MyBundle\\Entity\\Person",
                "sentAs":"json",
                "required":"true"
            }
        }
    }

Executing the `CreatePerson` command will now send an instance of `Vendor\MyBundle\Entity\Person` as JSON:

    $command = $client->getCommand('CreatePerson', array('person' => $person));
    $client->execute($command);

Concrete commands
-----------------

When using a concrete command, you can still make use of the JMSSerializer by implementating `Misd\GuzzleBundle\Service\Command\JMSSerializerAwareCommandInterface`.
