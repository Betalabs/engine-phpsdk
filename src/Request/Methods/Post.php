<?php

namespace Betalabs\Engine\Request\Methods;

class Post extends Request
{

    public function send($path, $data, $withApiPrefix = true)
    {
        return $this->processContents(
            $this->client->post(
                $this->uri($path, $withApiPrefix),
                $this->buildOptions($data)
            )
        );
    }

}