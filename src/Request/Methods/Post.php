<?php

namespace Betalabs\Engine\Request\Methods;

class Post extends Request
{

    public function send($path, $data)
    {
        return $this->processContents(
            $this->client->post(
                $this->uri($path),
                $this->buildOptions($data)
            )
        );
    }

}