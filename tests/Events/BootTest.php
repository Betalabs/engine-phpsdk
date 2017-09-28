<?php

namespace Betalabs\Engine\Tests\Events;

use Aura\Router\Map;
use Aura\Router\Matcher;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\RouteProvider;
use Betalabs\Engine\Routes\Boot;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Configs\RouteProvider as RouteProviderConfig;
use Betalabs\Engine\Routes\Reserved;
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
        $reserved = $this->mockReserved();

        $boot = new Boot($routerContainer, $routeProvider, $reserved, $token);
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
        $reserved = $this->mockReserved();

        $boot = new Boot($routerContainer, $routeProvider, $reserved, $token);
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
        $reserved = $this->mockReserved();

        $boot = new Boot($routerContainer, $routeProvider, $reserved, $token);
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
        $reserved = $this->mockReserved();

        $boot = new Boot($routerContainer, $routeProvider, $reserved, $token);
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
        $reserved = $this->mockReserved();

        $boot = new Boot($routerContainer, $routeProvider, $reserved, $token);
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

        $router = \Mockery::mock(RouteProvider::class);
        $router->shouldReceive('route')
            ->once();

        $routeProvider = \Mockery::mock(RouteProviderConfig::class);
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

    protected function mockReserved()
    {
        $reserved = \Mockery::mock(Reserved::class);
        $reserved->shouldReceive('route')
            ->once();
        return $reserved;
    }

}