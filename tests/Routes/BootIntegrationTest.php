<?php

namespace Betalabs\Engine\Tests\Routes;

use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\RouteProvider as RouteProviderConfig;
use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Routes\Boot;
use Betalabs\Engine\Routes\Reserved;
use Betalabs\Engine\Tests\Fixtures\FakeRouteProvider;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;
use Laminas\Diactoros\ServerRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Boot with real Laminas request, real Aura router and real reserved routes
 * (only the route provider config and the token are mocked)
 */
class BootIntegrationTest extends TestCase
{

    /** @var \Aura\Router\RouterContainer */
    protected $routerContainer;

    protected function setUp(): void
    {
        $this->routerContainer = new RouterContainer();
    }

    public function testMatchedRouteReceivesPathAttributes()
    {

        $boot = $this->boot($this->mockToken(0));

        $this->assertEquals(
            'hello engine',
            $boot->start($this->serverRequest('/hello/engine'))
        );

    }

    public function testUnknownRouteThrowsNotFound()
    {

        $boot = $this->boot($this->mockToken(0));

        $this->expectException(NotFoundHttpException::class);

        $boot->start($this->serverRequest('/does-not-exist'));

    }

    public function testWrongMethodThrowsNotFound()
    {

        $boot = $this->boot($this->mockToken(0));

        $this->expectException(NotFoundHttpException::class);

        $boot->start($this->serverRequest('/hello/engine', 'POST'));

    }

    public function testReservedRoutesAreRegistered()
    {

        $boot = $this->boot($this->mockToken(0));
        $boot->start($this->serverRequest('/hello/engine'));

        $map = $this->routerContainer->getMap();

        $this->assertEquals('/boot/permission', $map->getRoute('permission-boot')->path);
        $this->assertEquals('/boot/database', $map->getRoute('database-boot')->path);
        $this->assertEquals('/boot/genesis', $map->getRoute('genesis-boot')->path);

    }

    public function testAllEngineHeadersInformTokenAndEndpoint()
    {

        // Exactly one argument: refresh token and expiration are not stored
        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('informToken')
            ->once()
            ->with('token-hash');

        $boot = $this->boot($token);
        $boot->start($this->serverRequest('/hello/engine', 'GET', [
            'engine-endpoint' => 'http://engine.endpoint',
            'engine-access-token' => 'token-hash',
            'engine-refresh-token' => 'refresh-hash',
            'engine-token-expires-at' => '9999999',
        ]));

        $this->assertEquals('http://engine.endpoint', $this->currentEndpoint());

    }

    public function testNonNumericExpirationHeaderDoesNotBreakTheRequest()
    {

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('informToken')
            ->once()
            ->with('token-hash');

        $boot = $this->boot($token);

        $this->assertEquals(
            'hello engine',
            $boot->start($this->serverRequest('/hello/engine', 'GET', [
                'engine-endpoint' => 'http://engine.endpoint',
                'engine-access-token' => 'token-hash',
                'engine-refresh-token' => 'refresh-hash',
                'engine-token-expires-at' => 'not-a-timestamp',
            ]))
        );

    }

    public function testStartAcceptsAnyPsr7ServerRequest()
    {

        $boot = $this->boot($this->mockToken(0));

        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', 'http://app.engine.test/hello/guzzle');

        $this->assertEquals('hello guzzle', $boot->start($request));

    }

    public function testIncompleteEngineHeadersAreIgnored()
    {

        $boot = $this->boot($this->mockToken(0));
        $boot->start($this->serverRequest('/hello/engine', 'GET', [
            'engine-endpoint' => 'http://engine.endpoint',
            'engine-access-token' => 'token-hash',
        ]));

        $this->assertNull($this->currentEndpoint());

    }

    protected function boot(Token $token)
    {

        $routeProvider = \Mockery::mock(RouteProviderConfig::class);
        $routeProvider->shouldReceive('routeProvider')
            ->once()
            ->andReturn(new FakeRouteProvider());

        return new Boot(
            $this->routerContainer,
            $routeProvider,
            new Reserved(new Container()),
            $token
        );

    }

    protected function mockToken($times)
    {

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('informToken')
            ->times($times);

        return $token;

    }

    protected function serverRequest($path, $method = 'GET', array $headers = [])
    {
        return new ServerRequest(
            [],
            [],
            'http://app.engine.test' . $path,
            $method,
            'php://input',
            $headers
        );
    }

    protected function currentEndpoint()
    {

        $property = new \ReflectionProperty(EndpointResolver::class, 'endpoint');

        return $property->getValue();

    }

}
