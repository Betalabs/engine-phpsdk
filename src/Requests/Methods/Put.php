<?php

namespace Betalabs\Engine\Requests\Methods;

class Put extends Request
{

    public function send($path, $data)
    {
        return $this->processContents(
            $this->client->put(
                $this->uri($path),
                $this->buildOptions($data)
            )
        );
    }

}