<?php

namespace Betalabs\Engine\Requests\Methods;

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