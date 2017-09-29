<?php

namespace Betalabs\Engine\Migration;

use Betalabs\Engine\Configs\MigrationProvider;
use Betalabs\Engine\Configs\Exceptions\MigrationProviderNotDefinedException;
use Betalabs\Engine\Requests\BootResponse;

class Boot
{

    /** @var \Betalabs\Engine\Configs\MigrationProvider */
    protected $databaseProvider;

    /**
     * Boot constructor.
     *
     * @param \Betalabs\Engine\Configs\MigrationProvider $databaseProvider
     */
    public function __construct(MigrationProvider $databaseProvider)
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
                    ->migrationProvider()
                    ->run();

        } catch(MigrationProviderNotDefinedException $e) {

            $bootResponse = new BootResponse(
                true,
                'Migration configuration not informed'
            );

        }

        return $bootResponse->formatResponse();

    }

}