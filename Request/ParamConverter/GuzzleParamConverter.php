<?php

/*
 * This file is part of the Symfony2 GuzzleBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\GuzzleBundle\Request\ParamConverter;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Service\ClientInterface;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * GuzzleParamConverter.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class GuzzleParamConverter implements ParamConverterInterface
{
    /**
     * @var ClientInterface[]
     */
    private $clients = array();

    /**
     * Make the param converter aware of a client.
     *
     * @param string          $id
     * @param ClientInterface $client
     */
    public function registerClient($id, ClientInterface $client)
    {
        $this->clients[$id] = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $found = $this->find($configuration);

        $name = $configuration->getName();
        $class = $configuration->getClass();
        $options = $configuration->getOptions();

        $client = $found['client'];
        $command = $found['command'];
        $operation = $client->getDescription()->getOperation($command);

        $routeParameters = $request->attributes->get('_route_params');

        if (true === isset($options['exclude'])) {
            foreach ($options['exclude'] as $exclude) {
                unset($routeParameters[$exclude]);
            }
        }

        if (false === isset($options['mapping'])) {
            $options['parameters'] = $routeParameters;
        } else {
            $options['parameters'] = array();
            foreach ($options['mapping'] as $key => $parameter) {
                $options['parameters'][$parameter] = $routeParameters[$key];
            }
        }

        $parameters = array();

        foreach ($options['parameters'] as $key => $value) {
            if ($operation->hasParam($key)) {
                switch ($operation->getParam($key)->getType()) {
                    case 'integer':
                        $value = (int) $value;
                }
                $parameters[$key] = $value;
            }
        }

        $command = $client->getCommand($command, $parameters);

        try {
            $result = $client->execute($command);
        } catch (BadResponseException $e) {
            if (true === $configuration->isOptional()) {
                $result = null;
            } elseif (404 === $e->getResponse()->getStatusCode()) {
                throw new NotFoundHttpException(sprintf('%s object not found.', $class), $e);
            } else {
                throw $e;
            }
        }

        $request->attributes->set($name, $result);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return null !== $this->find($configuration);
    }

    /**
     * Try and find a command for the requested class.
     *
     * @param ConfigurationInterface $configuration
     *
     * @return array|null Array containing the client and command if found, null if not.
     *
     * @throws LogicException
     */
    protected function find(ConfigurationInterface $configuration)
    {
        $options = $configuration->getOptions();

        // determine the client, if possible
        if (true === isset($options['client'])) {
            if (false === isset($this->clients[$options['client']])) {
                throw new LogicException(sprintf('Unknown client \'%s\'', $options['client']));
            }
            $client = $this->clients[$options['client']];
        } else {
            $client = null;
        }

        // determine the command, if possible
        if (true === isset($options['command'])) {
            if (null === $client) {
                throw new LogicException('Command defined without a client');
            }
            $operations = $client->getDescription()->getOperations();
            if (false === isset($operations[$options['command']])) {
                throw new LogicException(sprintf(
                    'Unknown command \'%s\' for client \'%s\'',
                    $options['command'],
                    $options['client']
                ));
            }

            $command = $options['command'];

            if (false === ($configuration->getClass() === $operations[$options['command']]->getResponseClass())) {
                throw new LogicException(sprintf(
                    'Command \'%s\' return \'%s\' rather than \'%s\'',
                    $options['command'],
                    $operations[$options['command']]->getResponseClass(),
                    $configuration->getClass()
                ));
            }
        } else {
            $command = null;
        }

        // if we don't know the command yet, try and find it
        if (null === $command) {
            if (null !== $client) {
                $searchClients = array($options['client'] => $client);
            } else {
                $searchClients = $this->clients;
            }
            foreach ($searchClients as $thisClient) {
                if (null === $thisClient->getDescription()) {
                    continue;
                }

                foreach ($thisClient->getDescription()->getOperations() as $operation) {
                    if (
                        'GET' === $operation->getHttpMethod() &&
                        $configuration->getClass() === $operation->getResponseClass()
                    ) {
                        $client = $thisClient;
                        $command = $operation->getName();

                        break 2;
                    }
                }
            }
        }

        if (null !== $client && null !== $command) {
            return array('client' => $client, 'command' => $command);
        } else {
            return null;
        }
    }
}
