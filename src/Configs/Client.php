<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException;
use Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException;
use DI\ContainerBuilder;
use DI\Container;

class Client
{

    /** @var \Betalabs\Engine\Configs\Reader */
    protected $reader;

    /** @var \Betalabs\Engine\Configs\Helper */
    protected $helper;

    /** @var \DI\Container */
    protected $container;

    public function __construct(
        Reader $reader,
        Helper $helper,
        Container $container = null
    ) {
        $this->reader = $reader;
        $this->helper = $helper;
        $this->container = $container ?? ContainerBuilder::buildDevContainer();
    }

    /**
     * Search for id property on client node
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     */
    public function id()
    {

        $client = $this->clientNode();

        if(isset($client->id)) {
            return (string) $client->id;
        }

        throw new PropertyNotFoundException('Property id of client node not found');

    }

    /**
     * Search for secret property on client node
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     */
    public function secret()
    {

        $client = $this->clientNode();

        if(isset($client->secret)) {
            return (string) $client->secret;
        }

        throw new PropertyNotFoundException('Property secret of client node not found');

    }

    /**
     * Search for client node in configuration file
     *
     * @return \SimpleXMLElement[]
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     */
    protected function clientNode()
    {

        if(isset($this->reader->load()->client)) {
            return $this->reader->load()->client;
        }

        throw new ClientNotDefinedException('client node does not exist in configuration file');

    }

}