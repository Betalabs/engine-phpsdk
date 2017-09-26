<?php

namespace Betalabs\Engine\Tests\Request\Methods;

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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header])
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

        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([$client, $header])
            ->getMockForAbstractClass();

        $this->assertEquals(
            $request,
            $request->mustNotAuthorize()
        );

    }

    public function testUriWithEndpointSuffix()
    {

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $request->setEndpoint('http://engine.local/');

        $this->assertEquals(
            'http://engine.local/api/path/to/api',
            $request->uri('path/to/api')
        );

    }

    public function testUriWithAnotherEndpointSuffix()
    {

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $request->setEndpoint('http://engine.local/');
        $request->setEndpointSuffix('not-api');

        $this->assertEquals(
            'http://engine.local/not-api/path/to/api',
            $request->uri('path/to/api')
        );

    }

    public function testUriWithoutEndpointSuffix()
    {

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $request->setEndpoint('http://engine.local/');
        $request->setEndpointSuffix(null);

        $this->assertEquals(
            'http://engine.local/path/to/api',
            $request->uri('path/to/api')
        );

    }

}