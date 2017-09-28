<?php

namespace Betalabs\Engine\Tests\Routes;

use Aura\Router\Map;
use Betalabs\Engine\Database\Boot as DatabaseBoot;
use Betalabs\Engine\Permissions\Boot as PermissionBoot;
use Betalabs\Engine\Routes\Reserved;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class ReservedTest extends TestCase
{

    public function testAllRouteIsIncluded()
    {

        $container = \Mockery::mock(Container::class);

        $reserved = new Reserved($container);

        $map = \Mockery::mock(Map::class);
        $map->shouldReceive('get')
            ->times(2);

        $this->assertNull($reserved->route($map));

    }

    public function testPermissionRouteResponse()
    {

        $boot = \Mockery::mock(PermissionBoot::class);
        $boot->shouldReceive('render')
            ->andReturn('success');

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(PermissionBoot::class)
            ->andReturn($boot);

        $reserved = new Reserved($container);

        $this->assertEquals(
            json_encode([
                'data' => 'success'
            ]),
            $reserved->responsePermissionBoot()
        );

    }

    public function testDatabaseRouteResponse()
    {

        $boot = \Mockery::mock(DatabaseBoot::class);
        $boot->shouldReceive('run')
            ->andReturn('success');

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(DatabaseBoot::class)
            ->andReturn($boot);

        $reserved = new Reserved($container);

        $this->assertEquals(
            json_encode([
                'data' => 'success'
            ]),
            $reserved->responseDatabaseBoot()
        );

    }

}