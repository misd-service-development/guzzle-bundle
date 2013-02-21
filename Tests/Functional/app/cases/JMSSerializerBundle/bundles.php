<?php

/*
 * This file is part of the MisdGuzzleBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array(
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new JMS\SerializerBundle\JMSSerializerBundle($this),
    new Misd\GuzzleBundle\MisdGuzzleBundle(),
);
