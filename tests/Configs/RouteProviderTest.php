<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Exceptions\RouteClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteFileDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\RouteProviderNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Configs\RouteProvider;
use Betalabs\Engine\RouteProvider;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class RouteProviderTest extends TestCase
{

    public function testExistingRouteProviderConfigWithPathAndClass()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'routeProvider' => (object) [
                    'path' => 'path/to/class.php',
                    'class' => 'ClassName'
                ]
            ]);

        $reader->shouldReceive('getRootPath')
            ->once()
            ->andReturn('/path/to/root');

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->once()
            ->with('ClassName')
            ->andReturn(true);

        $helper->shouldReceive('fileExists')
            ->once()
            ->with('/path/to/root/path/to/class.php')
            ->andReturn(true);

        $helper->shouldReceive('requireFileOnce')
            ->once()
            ->with('/path/to/root/path/to/class.php')
            ->andReturn(true);

        $router = \Mockery::mock(RouteProvider::class);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->once()
            ->with('ClassName')
            ->andReturn($router);

        $routeProvider = new RouteProvider($reader, $helper, $container);

        $this->assertEquals(
            $router,
            $routeProvider->routeProvider()
        );

    }

    public function testExistingRouteProviderConfigWithClass()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'routeProvider' => (object) [
                    'class' => 'ClassName'
                ]
            ]);

        $reader->shouldReceive('getRootPath')
            ->times(0)
            ->andReturn('/path/to/root');

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->once()
            ->with('ClassName')
            ->andReturn(true);

        $router = \Mockery::mock(RouteProvider::class);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->once()
            ->with('ClassName')
            ->andReturn($router);

        $routeProvider = new RouteProvider($reader, $helper, $container);

        $this->assertEquals(
            $router,
            $routeProvider->routeProvider()
        );

    }

    public function testExistingRouteProviderConfigWithPathUnexisting()
    {

        $this->expectException(RouteFileDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'routeProvider' => (object) [
                    'path' => 'path/to/class.php',
                    'class' => 'ClassName'
                ]
            ]);

        $reader->shouldReceive('getRootPath')
            ->andReturn('/path/to/root');

        $helper = \Mockery::mock(Helper::class);

        $helper->shouldReceive('fileExists')
            ->once()
            ->with('/path/to/root/path/to/class.php')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $routeProvider = new RouteProvider($reader, $helper, $container);

        $routeProvider->routeProvider();

    }

    public function testExistingRouteProviderConfigWithClassUnexisting()
    {

        $this->expectException(RouteClassDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'routeProvider' => (object) [
                    'class' => 'ClassName'
                ]
            ]);

        $reader->shouldReceive('getRootPath')
            ->times(0)
            ->andReturn('/path/to/root');

        $helper = \Mockery::mock(Helper::class);

        $helper->shouldReceive('classExists')
            ->once()
            ->with('ClassName')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $routeProvider = new RouteProvider($reader, $helper, $container);

        $routeProvider->routeProvider();

    }

    public function testRouteProviderNodeDoesNotExist()
    {

        $this->expectException(RouteProviderNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) []);

        $reader->shouldReceive('getRootPath')
            ->times(0)
            ->andReturn('/path/to/root');

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $routeProvider = new RouteProvider($reader, $helper, $container);

        $routeProvider->routeProvider();

    }

}