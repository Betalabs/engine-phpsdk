<?php

// Namespace intentionally outside PSR-4 autoload: the class only exists
// after the file is loaded through requireClassPath()
namespace BetalabsEngineTestsRequired;

use Aura\Router\Map;
use Betalabs\Engine\RouteProvider;

class RequiredRouteProvider implements RouteProvider
{

    /**
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(Map $map)
    {

    }

}
