<?php

class Genesis implements \Betalabs\Engine\GenesisProvider
{

    /**
     * Run genesis boot
     *
     * @return \Betalabs\Engine\Requests\BootResponse
     */
    public function run()
    {
        return new \Betalabs\Engine\Requests\BootResponse(
            true,
            'It also works!'
        );
    }

}