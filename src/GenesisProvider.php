<?php

namespace Betalabs\Engine;

interface GenesisProvider
{

    /**
     * Run genesis boot
     *
     * @return \Betalabs\Engine\Requests\BootResponse
     */
    public function run();

}