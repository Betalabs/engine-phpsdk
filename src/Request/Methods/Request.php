<?php

namespace Betalabs\Engine\Request\Methods;

use Betalabs\Engine\Request\Header;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Request
{

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var mixed */
    protected $contents;

    /** @var \Betalabs\Engine\Request\Header */
    protected $header;

    /** @var string */
    protected $endpoint = 'http://engine.local/';

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Request constructor.
     * @param \GuzzleHttp\Client $client
     * @param \Betalabs\Engine\Request\Header $header
     */
    public function __construct(Client $client, Header $header)
    {
        $this->client = $client;
        $this->header = $header;
    }

    /**
     * Build URI
     *
     * @param string $path
     * @param bool $withApiPrefix
     * @return string
     */
    protected function uri($path, $withApiPrefix = true)
    {

        $prefix = $withApiPrefix ? 'api/' : '';

        return $this->endpoint . $prefix . $path;

    }

    /**
     * Build the options for Guzzle based on given data
     *
     * @param array $data
     * @return array
     */
    protected function buildOptions($data = null)
    {

        $options['headers'] = $this->header->headers();

        if(is_null($data)) {
            return $options;
        }

        $options['json'] = $data;
        return $options;

    }

    /**
     * Store the request and return the body
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return mixed
     */
    protected function processContents(ResponseInterface $response)
    {

        $this->response = $response;

        return $this->contents = json_decode(
            $this->response->getBody()->getContents()
        );

    }

    /**
     * Gets the response status code
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Define authorization is required
     *
     * @return $this
     */
    public function mustAuthorize()
    {
        $this->header->mustAuthorize();
        return $this;
    }

    /**
     * Define authorization is not required
     *
     * @return $this
     */
    public function mustNotAuthorize()
    {
        $this->header->mustNotAuthorize();
        return $this;
    }

}