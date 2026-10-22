<?php

namespace Betalabs\Engine\Tests\Fixtures;

use Aura\Router\Map;
use Betalabs\Engine\RouteProvider;

class FakeRouteProvider implements RouteProvider
{

    /**
     * Declare a test route that returns the {name} attribute
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(Map $map)
    {

        $map->get(
            'fake-hello',
            '/hello/{name}',
            function($request) {
                return 'hello ' . $request->getAttribute('name');
            }
        );

    }

}
