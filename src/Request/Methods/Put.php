<?php

namespace Betalabs\Engine\Request\Methods;

class Put extends Request
{

    public function send($path, $data, $withApiPrefix = true)
    {
        return $this->processContents(
            $this->client->put(
                $this->uri($path, $withApiPrefix),
                $this->buildOptions($data)
            )
        );
    }

}