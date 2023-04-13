<?php

namespace Betalabs\Engine\Routes;

use Laminas\Diactoros\ServerRequestFactory;

class Request
{

    /**
     * Build request based on GLOBALS
     *
     * @return \Laminas\Diactoros\ServerRequest
     */
    public function buildRequest()
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

}