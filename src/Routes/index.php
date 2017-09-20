<?php

require_once __DIR__ .'/../../vendor/autoload.php';

/*
 * Initialize container for automatic dependency injection
 */
$container = \DI\ContainerBuilder::buildDevContainer();

/*
 * Create an object based on server's data
 */
require_once __DIR__ .'/Request.php';
$request = $container->get(\Betalabs\Engine\Routes\Request::class);

/*
 * Boot application calling appropriate route
 */
require_once __DIR__ .'/Boot.php';
$boot = $container->get(\Betalabs\Engine\Routes\Boot::class);

try {
    $boot->start($request->buildRequest());
} catch(\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
    http_response_code($exception->getStatusCode());
}