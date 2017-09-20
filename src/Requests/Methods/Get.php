<?php

namespace Betalabs\Engine\Requests\Methods;

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