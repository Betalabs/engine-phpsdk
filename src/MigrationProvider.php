<?php

namespace Betalabs\Engine;

interface MigrationProvider
{

    /**
     * Run database migration
     *
     * @return \Betalabs\Engine\Migration\BootResponse
     */
    public function run();

}