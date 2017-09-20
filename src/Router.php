<?php

namespace Betalabs\Engine;

interface Router
{

    /**
     * Declare routes
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(\Aura\Router\Map $map);

}