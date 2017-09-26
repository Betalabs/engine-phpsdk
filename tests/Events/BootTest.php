<?php

namespace Betalabs\Engine\Tests\Events;

use Aura\Router\Map;
use Aura\Router\Matcher;
use Betalabs\Engine\Router;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Routes\Boot;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Configs\RouteProvider;
use Betalabs\Engine\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Diactoros\ServerRequest;

class BootTest extends TestCase
{

    public function testValidRouteWithAllEngineHeaders()
    {

        $routerContainer = $this->mockRouterContainer();
        $routeProvider = $this->mockRouteProvider();
        $token = $this->mockToken();

        $boot = new Boot($routerContainer, $routeProvider, $token);
        $this->assertEquals(
            'success',
            $boot->start($this->mockServerRequest())
        );

    }

    public function testValidRouteWithNoEngineHeaders()
    {

        $routerContainer = $this->mockRouterContainer();
        $routeProvider = $this->mockRouteProvider();
        $token = $this->mockToken(0);

        $boot = new Boot($routerContainer, $routeProvider, $token);
        $this->assertEquals(
            'success',
            $boot->start(
                $this->mockServerRequest('', '', '')
            )
        );

    }

    public function testValidRouteWithTokenEngineHeaderOnly()
    {

        $routerContainer = $this->mockRouterContainer();
        $routeProvider = $this->mockRouteProvider();
        $token = $this->mockToken(0);

        $boot = new Boot($routerContainer, $routeProvider, $token);
        $this->assertEquals(
            'success',
            $boot->start(
                $this->mockServerRequest('token-hash','', '')
            )
        );

    }

    public function testValidRouteWithExpiresAtEngineHeaderOnly()
    {

        $routerContainer = $this->mockRouterContainer();
        $routeProvider = $this->mockRouteProvider();
        $token = $this->mockToken(0);

        $boot = new Boot($routerContainer, $routeProvider, $token);
        $this->assertEquals(
            'success',
            $boot->start(
                $this->mockServerRequest('')
            )
        );

    }

    public function testInvalidRoute()
    {

        $this->expectException(NotFoundHttpException::class);

        $routerContainer = $this->mockRouterContainer(false);
        $routeProvider = $this->mockRouteProvider();
        $token = $this->mockToken(0);

        $boot = new Boot($routerContainer, $routeProvider, $token);
        $boot->start(
            $this->mockServerRequest('')
        );

    }

    protected function mockRouterContainer($match = true)
    {

        $routerContainer = \Mockery::mock(RouterContainer::class);
        $routerContainer->shouldReceive('getMap')
            ->once()
            ->andReturn(\Mockery::mock(Map::class));

        $matcher = \Mockery::mock(Matcher::class);
        $matcher->shouldReceive('match')
            ->once()
            ->andReturn($match ? ((object)[
                'attributes' => [],
                'handler' => function () {
                    return 'success';
                }
            ]) : false);

        $routerContainer->shouldReceive('getMatcher')
            ->once()
            ->andReturn($matcher);

        return $routerContainer;

    }

    protected function mockRouteProvider()
    {

        $router = \Mockery::mock(Router::class);
        $router->shouldReceive('route')
            ->once();

        $routeProvider = \Mockery::mock(RouteProvider::class);
        $routeProvider->shouldReceive('routeProvider')
            ->once()
            ->andReturn($router);

        return $routeProvider;

    }

    protected function mockToken($times = 1)
    {

        $token = \Mockery::mock(Token::class);

        $token->shouldReceive('informToken')
            ->times($times);

        return $token;

    }

    protected function mockServerRequest(
        $accessToken = 'token-hash',
        $refreshToken = 'refresh-hash',
        $expiresAt = 9999999
    ) {

        $serverRequest = \Mockery::mock(ServerRequest::class);

        $serverRequest->shouldReceive('getHeaderLine')
            ->once()
            ->with('engine-access-token')
            ->andReturn($accessToken);

        $serverRequest->shouldReceive('getHeaderLine')
            ->once()
            ->with('engine-refresh-token')
            ->andReturn($refreshToken);

        $serverRequest->shouldReceive('getHeaderLine')
            ->once()
            ->with('engine-token-expires-at')
            ->andReturn($expiresAt);

        return $serverRequest;

    }

}