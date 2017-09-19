<?php

namespace Betalabs\Engine\Configs;

use DI\ContainerBuilder;

class RouteProvider
{

    /** @var \Betalabs\Engine\Configs\Reader */
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function routeProvider()
    {

        $config = $this->reader->load()->routeProvider;

        if(isset($config->path) && !empty($config->path)) {
            $path = $this->reader->getRootPath() . $config->path;
            require_once $path;
        }

        $container = ContainerBuilder::buildDevContainer();
        return $container->get((string) $config->class);

    }

}