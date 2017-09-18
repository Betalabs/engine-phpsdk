<?php

namespace Betalabs\Engine\Tests\Request\Methods;

use Betalabs\Engine\Request\Header;
use Betalabs\Engine\Request\Methods\Request;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

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

    public function testUriWithEndpointSufix()
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

    public function testUriWithAnotherEndpointSufix()
    {

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $request->setEndpoint('http://engine.local/');
        $request->setEndpointSufix('not-api');

        $this->assertEquals(
            'http://engine.local/not-api/path/to/api',
            $request->uri('path/to/api')
        );

    }

    public function testUriWithoutEndpointSufix()
    {

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $request->setEndpoint('http://engine.local/');
        $request->setEndpointSufix(null);

        $this->assertEquals(
            'http://engine.local/path/to/api',
            $request->uri('path/to/api')
        );

    }

}