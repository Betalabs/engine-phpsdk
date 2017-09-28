<?php

namespace Betalabs\Engine\Routes;

use Aura\Router\Map;
use Betalabs\Engine\Permissions\Boot;
use Betalabs\Engine\RouteProvider;
use DI\Container;

class Reserved implements RouteProvider
{

    /** @var \DI\Container */
    protected $container;

    /**
     * Reserved constructor.
     * @param \DI\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Declare reserved routes
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(Map $map)
    {
        $this->permissionBoot($map);
    }

    /**
     * Map permission boot
     *
     * @param \Aura\Router\Map $map
     */
    protected function permissionBoot(Map $map)
    {

        $map->get(
            'permission-boot',
            '/boot/permission',
            function() {
                echo $this->responsePermissionBoot();
            }
        );

    }

    /**
     * Build permission boot route response
     *
     * @return string
     */
    public function responsePermissionBoot()
    {

        $boot = $this->container->get(Boot::class);

        return $this->buildEngineDefaultResponse($boot->render());

    }

    /**
     * Render response array in Engine default
     *
     * By default Engine always receive data in JSON format with a data
     * property
     *
     * @param $data
     * @return string
     */
    protected function buildEngineDefaultResponse($data)
    {
        return json_encode([
            'data' => $data
        ]);
    }

}