<?php

namespace Betalabs\Engine;

use Aura\Router\Map;

interface Router
{

    /**
     * Declare routes
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(Map $map);

}