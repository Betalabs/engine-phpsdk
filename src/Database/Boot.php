<?php

namespace Betalabs\Engine\Database;

use Betalabs\Engine\Configs\DatabaseProvider;

class Boot
{

    /** @var \Betalabs\Engine\Configs\DatabaseProvider */
    protected $databaseProvider;

    /**
     * Boot constructor.
     *
     * @param \Betalabs\Engine\Configs\DatabaseProvider $databaseProvider
     */
    public function __construct(DatabaseProvider $databaseProvider)
    {
        $this->databaseProvider = $databaseProvider;
    }

    /**
     * Run database migration
     *
     * @return array
     */
    public function run()
    {
        return $this->databaseProvider
                ->databaseProvider()
                ->run()
                ->formatResponse();

    }

}