<?php

namespace Betalabs\Engine\Tests\Request;

use Betalabs\Engine\Request\Header;
use PHPUnit\Framework\TestCase;
use Betalabs\Engine\Auth\Header as AuthHeader;

class HeaderTest extends TestCase
{

    public function testMustAuthorizeCallsHeaderMethod()
    {

        $authHeader = \Mockery::mock(AuthHeader::class);
        $authHeader->shouldReceive('mustAuthorize')
            ->once();

        $header = new Header($authHeader);

        $this->assertEquals(
            $header,
            $header->mustAuthorize()
        );

    }

    public function testMustNotAuthorizeCallsHeaderMethod()
    {

        $authHeader = \Mockery::mock(AuthHeader::class);
        $authHeader->shouldReceive('mustNotAuthorize')
            ->once();

        $header = new Header($authHeader);

        $this->assertEquals(
            $header,
            $header->mustNotAuthorize()
        );

    }

    public function testHeadersReturnsAppropriateArray()
    {

        $authHeader = \Mockery::mock(AuthHeader::class);
        $authHeader->shouldReceive('header')
            ->once()
            ->andReturn([
                'Authorization' => 'Bearer hash'
            ]);

        $header = new Header($authHeader);

        $this->assertEquals(
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer hash'
            ],
            $header->headers()
        );

    }

}