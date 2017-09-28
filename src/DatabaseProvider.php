<?php

namespace Betalabs\Engine;

interface DatabaseProvider
{

    /**
     * Run database migration
     *
     * @return \Betalabs\Engine\Database\BootResponse
     */
    public function run();

}