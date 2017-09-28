<?php

namespace Betalabs\Engine;

interface RouteProvider
{

    /**
     * Declare routes
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(\Aura\Router\Map $map);

}