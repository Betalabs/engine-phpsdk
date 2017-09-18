<?php

namespace Betalabs\Engine\Request\Methods;

class Delete extends Request
{

    public function send($path, $data, $withApiPrefix = true)
    {
        return $this->processContents(
            $this->client->delete(
                $this->uri($path, $withApiPrefix),
                $this->buildOptions($data)
            )
        );
    }

}