<?php

namespace Betalabs\Engine;

use Betalabs\Engine\Configs\RouteProvider;
use Zend\Diactoros\ServerRequestFactory;
use Aura\Router\RouterContainer;
use Betalabs\Engine\Auth\Token;
use DI\ContainerBuilder;
use Carbon\Carbon;

class Listener
{

    /** @var \Betalabs\Engine\Auth\Token */
    protected $token;

    /**
     * Listener constructor.
     * @param \Betalabs\Engine\Auth\Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Listen requests
     */
    public function listen()
    {

        $routerContainer = new RouterContainer();

        $this->mapRoutes($routerContainer);

        $request = $this->buildRequest();

        $this->engineHeaders($request);
        $this->matchRoute($routerContainer, $request);

    }

    /**
     * Map routes using RouteProvider
     *
     * @param RouterContainer $routerContainer
     */
    protected function mapRoutes($routerContainer)
    {

        $container = ContainerBuilder::buildDevContainer();

        $routerProvider = $container->get(RouteProvider::class)->routeProvider();

        $routerProvider->route($routerContainer->getMap());

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
     * @param $routerContainer
     * @param $request
     * @return mixed
     */
    protected function matchRoute($routerContainer, $request)
    {

        $matcher = $routerContainer->getMatcher();
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