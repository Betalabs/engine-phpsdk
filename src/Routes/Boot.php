<?php

namespace Betalabs\Engine\Routes;

use Betalabs\Engine\Configs\RouteProvider;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Requests\EndpointResolver;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Laminas\Diactoros\ServerRequest;

class Boot
{

    /** @var \Aura\Router\RouterContainer */
    protected $routerContainer;

    /** @var \Betalabs\Engine\Configs\RouteProvider */
    protected $routeProvider;

    /** @var \Betalabs\Engine\Routes\Reserved */
    protected $reserved;

    /** @var \Betalabs\Engine\Auth\Token */
    protected $token;

    /**
     * Listener constructor.
     * @param \Aura\Router\RouterContainer $routerContainer
     * @param \Betalabs\Engine\Configs\RouteProvider $routeProvider
     * @param \Betalabs\Engine\Auth\Token $token
     * @param \Betalabs\Engine\Routes\Reserved $reserved
     */
    public function __construct(
        RouterContainer $routerContainer,
        RouteProvider $routeProvider,
        Reserved $reserved,
        Token $token
    ) {
        $this->routerContainer = $routerContainer;
        $this->routeProvider = $routeProvider;
        $this->reserved = $reserved;
        $this->token = $token;
    }

    /**
     * Start request
     *
     * @param \Laminas\Diactoros\ServerRequest $request
     * @return mixed
     */
    public function start(ServerRequest $request)
    {

        $this->mapRoutes();

        $this->engineHeaders($request);

        return $this->matchRoute($request);

    }

    /**
     * Map routes using RouteProvider
     *
     */
    protected function mapRoutes()
    {

        $map = $this->routerContainer->getMap();

        // Map App routes
        $routerProvider = $this->routeProvider->routeProvider();
        $routerProvider->route($map);

        // Map reserved routes
        $this->reserved->route($map);

    }

    /**
     * Search for Engine headers to assign token
     *
     * @param \Laminas\Diactoros\ServerRequest $request
     */
    protected function engineHeaders($request)
    {
        $endpoint = $request->getHeaderLine('engine-endpoint');
        $accessToken = $request->getHeaderLine('engine-access-token');
        $refreshToken = $request->getHeaderLine('engine-refresh-token');
        $expiresAt = $request->getHeaderLine('engine-token-expires-at');

        if(empty($endpoint) || empty($accessToken) || empty($refreshToken) || empty($expiresAt)) {
            return;
        }

        $this->token->informToken(
            $accessToken,
            $refreshToken,
            Carbon::createFromTimestamp($expiresAt)
        );

        EndpointResolver::setEndpoint($endpoint);
    }

    /**
     * Match the request with declared routes
     *
     * @param $request
     * @return mixed
     */
    protected function matchRoute($request)
    {

        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);

        $this->evaluateRoute($route);

        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        $callable = $route->handler;
        return $callable($request);
    }

    /**
     * If route does not exist throw 404 status code
     *
     * @param $route
     */
    protected function evaluateRoute($route)
    {

        if($route) {
            return;
        }

        throw new NotFoundHttpException();

    }

}