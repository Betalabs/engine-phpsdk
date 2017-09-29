<?php

namespace Betalabs\Engine\Routes;

use Aura\Router\Map;
use Betalabs\Engine\Migration\Boot as DatabaseBoot;
use Betalabs\Engine\Permissions\Boot as PermissionBoot;
use Betalabs\Engine\Genesis\Boot as GenesisBoot;
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

        $map->get(
            'permission-boot',
            '/boot/permission',
            function() {
                echo $this->responsePermissionBoot();
            }
        );

        $map->get(
            'database-boot',
            '/boot/database',
            function() {
                echo $this->responseDatabaseBoot();
            }
        );

        $map->get(
            'genesis-boot',
            '/boot/genesis',
            function() {
                echo $this->responseGenesisBoot();
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

        $boot = $this->container->get(PermissionBoot::class);

        return $this->buildEngineDefaultResponse($boot->render());

    }

    /**
     * Build database boot route response
     *
     * @return string
     */
    public function responseDatabaseBoot()
    {

        $boot = $this->container->get(DatabaseBoot::class);

        return $this->buildEngineDefaultResponse($boot->run());

    }

    /**
     * Build genesis boot route response
     *
     * @return string
     */
    public function responseGenesisBoot()
    {

        $boot = $this->container->get(GenesisBoot::class);

        return $this->buildEngineDefaultResponse($boot->run());

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