<?php

namespace Betalabs\Engine\Genesis;

use Betalabs\Engine\Configs\Exceptions\GenesisProviderNotDefinedException;
use Betalabs\Engine\Configs\GenesisProvider;
use Betalabs\Engine\Requests\BootResponse;

class Boot
{

    /** @var \Betalabs\Engine\Configs\GenesisProvider */
    protected $genesisProvider;

    /**
     * Boot constructor.
     * @param \Betalabs\Engine\Configs\GenesisProvider $genesisProvider
     */
    public function __construct(GenesisProvider $genesisProvider)
    {
        $this->genesisProvider = $genesisProvider;
    }

    /**
     * Run genesis boot
     *
     * @return array
     */
    public function run()
    {

        try {

            $bootResponse = $this->genesisProvider
                ->genesisProvider()
                ->run();

        } catch(GenesisProviderNotDefinedException $e) {

            $bootResponse = new BootResponse(
                true,
                'Genesis configuration not informed'
            );

        }

        return $bootResponse->formatResponse();

    }

}