<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\FileDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException;
use DI\ContainerBuilder;
use DI\Container;

class RouteProvider extends AbstractProvider
{

    /**
     * Build route provider based on configs
     *
     * @return \Betalabs\Engine\RouteProvider
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException
     */
    public function routeProvider()
    {

        if(!isset($this->reader->load()->routeProvider)) {
            throw new RouteProviderNotDefinedException(
                'routeProvider node does not exist in configuration file'
            );
        }

        $config = $this->reader->load()->routeProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new RouteClassDoesNotExistException(
                'Class '. $class .' defined in configuration does not exist'
            );
        }

        return $this->container->get($class);

    }

}