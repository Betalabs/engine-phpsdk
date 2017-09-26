<?php

namespace Betalabs\Engine\Routes;

use Betalabs\Engine\Configs\RouteProvider;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Diactoros\ServerRequest;

class Boot
{

    /** @var \Aura\Router\RouterContainer */
    protected $routerContainer;

    /** @var \Betalabs\Engine\Configs\RouteProvider */
    protected $routeProvider;

    /** @var \Betalabs\Engine\Auth\Token */
    protected $token;

    /**
     * Listener constructor.
     * @param \Aura\Router\RouterContainer $routerContainer
     * @param \Betalabs\Engine\Configs\RouteProvider $routeProvider
     * @param \Betalabs\Engine\Auth\Token $token
     */
    public function __construct(
        RouterContainer $routerContainer,
        RouteProvider $routeProvider,
        Token $token
    ) {
        $this->routerContainer = $routerContainer;
        $this->routeProvider = $routeProvider;
        $this->token = $token;
    }

    /**
     * Start request
     *
     * @param \Zend\Diactoros\ServerRequest $request
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

        $routerProvider = $this->routeProvider->routeProvider();

        $routerProvider->route($this->routerContainer->getMap());

    }

    /**
     * Search for Engine headers to assign token
     *
     * @param \Zend\Diactoros\ServerRequest $request
     */
    protected function engineHeaders($request)
    {

        $accessToken = $request->getHeaderLine('engine-access-token');
        $refreshToken = $request->getHeaderLine('engine-refresh-token');
        $expiresAt = $request->getHeaderLine('engine-token-expires-at');

        if(empty($accessToken) || empty($refreshToken) || empty($expiresAt)) {
            return;
        }

        $this->token->informToken(
            $accessToken,
            $refreshToken,
            Carbon::createFromTimestamp($expiresAt)
        );

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