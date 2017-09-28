<?php

namespace Betalabs\Engine\Routes;

use Aura\Router\Map;
use Betalabs\Engine\Permissions\Boot;
use Betalabs\Engine\RouteProvider;
use DI\Container;
use DI\ContainerBuilder;

class Reserved implements RouteProvider
{

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
            'boot/permission',
            function() {

                $container = ContainerBuilder::buildDevContainer();

                /** @var \Betalabs\Engine\Permissions\Boot $boot */
                $boot = $container->get(Boot::class);

                $this->buildEngineDefaultResponse($boot->render());

            }
        );

    }

    /**
     * Render response array in Engine default
     *
     * By default Engine always receive data in JSON format with a data
     * property
     *
     * @param $data
     */
    protected function buildEngineDefaultResponse($data)
    {
        echo json_encode([
            'data' => $data
        ]);
    }

}