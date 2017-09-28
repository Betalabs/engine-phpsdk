<?php

namespace Betalabs\Engine\Tests\Routes;

use Aura\Router\Map;
use Betalabs\Engine\Permissions\Boot;
use Betalabs\Engine\Routes\Reserved;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class ReservedTest extends TestCase
{

    public function testPermissionRouteIsIncluded()
    {

        $container = \Mockery::mock(Container::class);

        $reserved = new Reserved($container);

        $map = \Mockery::mock(Map::class);
        $map->shouldReceive('get')
            ->once();

        $this->assertNull($reserved->route($map));

    }

    public function testPermissionRouteResponse()
    {

        $boot = \Mockery::mock(Boot::class);
        $boot->shouldReceive('render')
            ->andReturn('success');

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(Boot::class)
            ->andReturn($boot);

        $reserved = new Reserved($container);

        $this->assertEquals(
            json_encode([
                'data' => 'success'
            ]),
            $reserved->responsePermissionBoot()
        );

    }

}