<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Service\Command\LocationVisitor\Request;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\LocationVisitor\Request\BodyVisitor;
use Guzzle\Service\Description\Parameter;
use JMS\Serializer\SerializerInterface;

/**
 * JMSSerializerBundle-enabled request body visitor.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class JMSSerializerBodyVisitor extends BodyVisitor
{
    /**
     * Serializer.
     *
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * Constructor.
     *
     * @param SerializerInterface|null $serializer Serializer, or null if not used.
     */
    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $filteredValue = $param->filter($value);

        if (null !== $this->serializer && (is_object($filteredValue) || is_array($filteredValue))) {
            switch ($param->getSentAs()) {
                case 'json':
                    $request->setHeader('Content-Type', 'application/json');
                    $contentType = 'json';
                    break;
                case 'yml':
                case 'yaml':
                    $request->setHeader('Content-Type', 'application/yaml');
                    $contentType = 'yml';
                    break;
                default:
                    $request->setHeader('Content-Type', 'application/xml');
                    $contentType = 'xml';
                    break;
            }
            $value = $this->serializer->serialize($filteredValue, $contentType);
        }

        parent::visit($command, $request, $param, $value);
    }
}
