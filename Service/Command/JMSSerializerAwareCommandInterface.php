<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Service\Command;

use Guzzle\Service\Command\CommandInterface;
use JMS\Serializer\SerializerInterface;

/**
 * Command which can use the JMSSerializer.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
interface JMSSerializerAwareCommandInterface extends CommandInterface
{
    /**
     * Set the JMSSerializer.
     *
     * @param SerializerInterface $serializer JMSSerializer.
     */
    public function setSerializer(SerializerInterface $serializer);
}
