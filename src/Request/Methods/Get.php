<?php

namespace Betalabs\Engine\Request\Methods;

class Get extends Request
{

    public function send($path, $withApiPrefix = true)
    {

        return $this->processContents(
            $this->client->get(
                $this->uri($path, $withApiPrefix),
                $this->buildOptions()
            )
        );

    }

}