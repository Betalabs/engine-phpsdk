<?php

namespace Betalabs\Engine\Request\Methods;

class Patch extends Request
{

    public function send($path, $data)
    {
        return $this->processContents(
            $this->client->patch(
                $this->uri($path),
                $this->buildOptions($data)
            )
        );
    }

}