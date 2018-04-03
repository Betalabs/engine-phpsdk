<?php

namespace Betalabs\Engine\Requests;

use Betalabs\Engine\Requests\Methods\Delete;
use Betalabs\Engine\Requests\Methods\Get;
use Betalabs\Engine\Requests\Methods\Patch;
use Betalabs\Engine\Requests\Methods\Post;
use Betalabs\Engine\Requests\Methods\Put;
use DI\ContainerBuilder;

class Request
{

    /** @var \DI\Container */
    protected $container;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->container = ContainerBuilder::buildDevContainer();
    }

    /**
     * Build a GET request
     *
     * @return \Betalabs\Engine\Requests\Methods\Get
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function get()
    {
        return $this->container->get(Get::class);
    }

    /**
     * Build a POST request
     *
     * @return \Betalabs\Engine\Requests\Methods\Post
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function post()
    {
        return $this->container->get(Post::class);
    }

    /**
     * Build a PUT request
     *
     * @return \Betalabs\Engine\Requests\Methods\Put
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function put()
    {
        return $this->container->get(Put::class);
    }

    /**
     * Build a PATCH request
     *
     * @return \Betalabs\Engine\Requests\Methods\Patch
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function patch()
    {
        return $this->container->get(Patch::class);
    }

    /**
     * Build a DELETE request
     *
     * @return \Betalabs\Engine\Requests\Methods\Delete
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function delete()
    {
        return $this->container->get(Delete::class);
    }

}