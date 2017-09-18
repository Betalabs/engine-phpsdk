<?php

namespace Betalabs\Engine\Request\Methods;

class Get extends Request
{

    public function send($path)
    {

        return $this->processContents(
            $this->client->get(
                $this->uri($path),
                $this->buildOptions()
            )
        );

    }

}