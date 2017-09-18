<?php

namespace Betalabs\Engine\Request;

use Betalabs\Engine\Request\Methods\Delete;
use Betalabs\Engine\Request\Methods\Get;
use Betalabs\Engine\Request\Methods\Patch;
use Betalabs\Engine\Request\Methods\Post;
use Betalabs\Engine\Request\Methods\Put;
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
     * @return \Betalabs\Engine\Request\Methods\Get
     */
    public function get()
    {
        return $this->container->get(Get::class);
    }

    /**
     * Build a POST request
     *
     * @return \Betalabs\Engine\Request\Methods\Post
     */
    public function post()
    {
        return $this->container->get(Post::class);
    }

    /**
     * Build a PUT request
     *
     * @return \Betalabs\Engine\Request\Methods\Put
     */
    public function put()
    {
        return $this->container->get(Put::class);
    }

    /**
     * Build a PATCH request
     *
     * @return \Betalabs\Engine\Request\Methods\Patch
     */
    public function patch()
    {
        return $this->container->get(Patch::class);
    }

    /**
     * Build a DELETE request
     *
     * @return \Betalabs\Engine\Request\Methods\Delete
     */
    public function delete()
    {
        return $this->container->get(Delete::class);
    }

}