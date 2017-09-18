<?php

namespace Betalabs\Engine\Request\Methods;

class Patch extends Request
{

    public function send($path, $data, $withApiPrefix = true)
    {
        return $this->processContents(
            $this->client->patch(
                $this->uri($path, $withApiPrefix),
                $this->buildOptions($data)
            )
        );
    }

}