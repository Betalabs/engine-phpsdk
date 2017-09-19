<?php

namespace Betalabs\Engine\Events;

use Betalabs\Engine\Configs\RouteProvider;
use Zend\Diactoros\ServerRequestFactory;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use Carbon\Carbon;

class Listener
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
     * Listen requests
     */
    public function listen()
    {

        $this->mapRoutes();

        $request = $this->buildRequest();

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
     * Build request based on GLOBALS
     *
     * @return \Zend\Diactoros\ServerRequest
     */
    protected function buildRequest()
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

    /**
     * Search for Engine headers to assign token
     *
     * @param \Zend\Diactoros\ServerRequest $request
     */
    protected function engineHeaders($request)
    {

        $token = $request->getHeaderLine('engine-token');
        $expiresAt = $request->getHeaderLine('engine-token-expires-at');

        if(empty($token) || empty($expiresAt)) {
            return;
        }

        $this->token->informBearerToken(
            $token,
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

        header("Status: 404 Not Found");
        echo 'Not found';
        exit;

    }

}