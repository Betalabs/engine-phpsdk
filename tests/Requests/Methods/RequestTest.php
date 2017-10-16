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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header, $endpoint])
            ->getMockForAbstractClass();

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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header, $endpoint])
            ->getMockForAbstractClass();

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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header, $endpoint])
            ->getMockForAbstractClass();

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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header, $endpoint])
            ->getMockForAbstractClass();

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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header, $endpoint])
            ->getMockForAbstractClass();

        $request->setEndpointSuffix(null);

        $this->assertEquals(
            'http://engine.test/path/to/api',
            $request->uri('path/to/api')
        );

    }

}