<?php

namespace Betalabs\Engine\Request\Methods;

class Delete extends Request
{

    public function send($path, $data)
    {
        return $this->processContents(
            $this->client->delete(
                $this->uri($path),
                $this->buildOptions($data)
            )
        );
    }

}