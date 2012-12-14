<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/** @Serializer\XmlRoot("person") */
class Person
{
    public function __toString()
    {
        return $this->firstName . ' ' . $this->familyName;
    }

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("integer")
     */
    public $id;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    public $firstName;

    /**
     * @Serializer\SerializedName("family-name")
     * @Serializer\Type("string")
     */
    public $familyName;
}
