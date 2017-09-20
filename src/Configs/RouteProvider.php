<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException;
use DI\ContainerBuilder;
use DI\Container;

class RouteProvider
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
     * Build Route based on configs
     *
     * @return \Betalabs\Engine\Router
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException
     */
    public function routeProvider()
    {

        if(!isset($this->reader->load()->routeProvider)) {
            throw new RouteProviderNotDefinedException('routeProvider node does not exist in configuration file');
        }

        $config = $this->reader->load()->routeProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new RouteClassDoesNotExistException('Class '. $class .' defined in configuration does not exist');
        }

        return $this->container->get($class);

    }

    /**
     * Require given path
     *
     * @param $config
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException
     */
    protected function requireClassPath($config)
    {

        $path = rtrim($this->reader->getRootPath(), '/') . '/' . ltrim($config->path, '/');

        if(!$this->helper->fileExists($path)) {
            throw new RouteFileDoesNotExistException('File '. $path .' defined in configuration does not exist');
        }

        $this->helper->requireFileOnce($path);

    }

}