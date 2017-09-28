<?php

namespace Betalabs\Engine\Database;

use Betalabs\Engine\Configs\DatabaseProvider;
use Betalabs\Engine\Configs\Exceptions\DatabaseProviderNotDefinedException;

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

        try {

            $bootResponse = $this->databaseProvider
                    ->databaseProvider()
                    ->run();

        } catch(DatabaseProviderNotDefinedException $e) {

            $bootResponse = new BootResponse(
                true,
                'Migration configuration not informed'
            );

        }

        return $bootResponse->formatResponse();

    }

}