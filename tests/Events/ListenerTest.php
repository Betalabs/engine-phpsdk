<?php

namespace Betalabs\Engine\Tests\Events;

use Aura\Router\Map;
use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\RouteProvider;
use Betalabs\Engine\Events\Listener;
use Betalabs\Engine\Router;
use PHPUnit\Framework\TestCase;

class ListenerTest extends TestCase
{

    public function testValidRoute()
    {

        $routerContainer = \Mockery::mock(RouterContainer::class);
        $routerContainer->shouldReceive('getMap')
            ->andReturn(\Mockery::mock(Map::class));

        $matcher = \Mockery::mock(Matcher::class);
        $matcher->shouldReceive('match')
            ->andReturn((object) [
                'attributes' => [],
                'handler' => function() {return 'success';}
            ]);

        $routerContainer->shouldReceive('getMatcher')
            ->andReturn($matcher);

        $router = \Mockery::mock(Router::class);
        $router->shouldReceive('route');

        $routeProvider = \Mockery::mock(RouteProvider::class);
        $routeProvider->shouldReceive('routeProvider')
            ->andReturn($router);

        $token = \Mockery::mock(Token::class);

        $listener = new Listener($routerContainer, $routeProvider, $token);
        $this->assertEquals(
            'success',
            $listener->listen()
        );

    }

//    public function testInvalidRoute()
//    {
//
//        $routerContainer = \Mockery::mock(RouterContainer::class);
//        $routerContainer->shouldReceive('getMap')
//            ->andReturn(\Mockery::mock(Map::class));
//
//        $matcher = \Mockery::mock(Matcher::class);
//        $matcher->shouldReceive('match')
//            ->andReturn(false);
//
//        $routerContainer->shouldReceive('getMatcher')
//            ->andReturn($matcher);
//
//        $router = \Mockery::mock(Router::class);
//        $router->shouldReceive('route');
//
//        $routeProvider = \Mockery::mock(RouteProvider::class);
//        $routeProvider->shouldReceive('routeProvider')
//            ->andReturn($router);
//
//        $token = \Mockery::mock(Token::class);
//
//        $listener = new Listener($routerContainer, $routeProvider, $token);
//        $listener->listen();
//
//    }

}