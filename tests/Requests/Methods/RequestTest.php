<?php

namespace Betalabs\Engine\Tests\Requests\Methods;

use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Requests\Header;
use Betalabs\Engine\Requests\Methods\Request;
use GuzzleHttp\Client;
use Betalabs\Engine\Tests\TestCase;

class RequestTest extends TestCase
{

    public function testMustAuthorizeCallsHeaderMethod()
    {

        $client = \Mockery::mock(Client::class);

        $header = \Mockery::mock(Header::class);
        $header->shouldReceive('mustAuthorize')
            ->once();

        $endpoint = \Mockery::mock(EndpointResolver::class);

        $request = $this->concreteRequest($client, $header, $endpoint);

        $this->assertEquals(
            $request,
            $request->mustAuthorize()
        );

    }

    public function testMustNotAuthorizeCallsHeaderMethod()
    {

        $client = \Mockery::mock(Client::class);

        $header = \Mockery::mock(Header::class);
        $header->shouldReceive('mustNotAuthorize')
            ->once();

        $endpoint = \Mockery::mock(EndpointResolver::class);

        $request = $this->concreteRequest($client, $header, $endpoint);

        $this->assertEquals(
            $request,
            $request->mustNotAuthorize()
        );

    }

    public function testUriWithEndpointSuffix()
    {

        $client = \Mockery::mock(Client::class);
        $header = \Mockery::mock(Header::class);

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('http://engine.local/');

        $request = $this->concreteRequest($client, $header, $endpoint);

        $this->assertEquals(
            'http://engine.local/api/path/to/api',
            $request->uri('path/to/api')
        );

    }

    public function testUriWithAnotherEndpointSuffix()
    {

        $client = \Mockery::mock(Client::class);
        $header = \Mockery::mock(Header::class);

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('http://engine.test/');

        $request = $this->concreteRequest($client, $header, $endpoint);

        $request->setEndpointSuffix('not-api');

        $this->assertEquals(
            'http://engine.test/not-api/path/to/api',
            $request->uri('path/to/api')
        );

    }

    public function testUriWithoutEndpointSuffix()
    {

        $client = \Mockery::mock(Client::class);
        $header = \Mockery::mock(Header::class);

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('http://engine.test/');

        $request = $this->concreteRequest($client, $header, $endpoint);

        $request->setEndpointSuffix(null);

        $this->assertEquals(
            'http://engine.test/path/to/api',
            $request->uri('path/to/api')
        );

    }

    /**
     * Request is abstract without abstract methods: an empty subclass replaces
     * getMockForAbstractClass(), removed in PHPUnit 12
     */
    protected function concreteRequest($client, $header, $endpoint)
    {
        return new class($client, $header, $endpoint) extends Request {};
    }

}