<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException;
use DI\Container;
use DI\ContainerBuilder;

abstract class AbstractProvider
{

    /** @var \DI\Container */
    protected $container;

    /** @var \Betalabs\Engine\Configs\Helper */
    protected $helper;

    /** @var \Betalabs\Engine\Configs\Reader */
    protected $reader;

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
     * Require given path
     *
     * @param $config
     * @throws \Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException
     */
    protected function requireClassPath($config)
    {

        $path = rtrim($this->reader->getRootPath(), '/') . '/' . ltrim($config->path, '/');

        if (!$this->helper->fileExists($path)) {
            throw new RouteFileDoesNotExistException('File ' . $path . ' defined in configuration does not exist');
        }

        $this->helper->requireFileOnce($path);

    }
}