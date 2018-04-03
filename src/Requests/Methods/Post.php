<?php

namespace Betalabs\Engine\Requests\Methods;

class Post extends Request
{

    /**
     * @param $path
     * @param $data
     *
     * @return mixed
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
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