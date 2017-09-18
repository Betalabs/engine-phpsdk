<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Header;
use Betalabs\Engine\Auth\Token;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{

    public function testHeaderDoesNotIncludeAuthorizationWhenMustNotAuthorize()
    {

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $header->mustNotAuthorize();

        $this->assertEmpty($header->header());

    }

    public function testHeaderIncludeAuthorizationWhenMustAuthorize()
    {

        $bearerToken = 'bearer-token-hash';

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('retrieveToken')
            ->andReturn($bearerToken);

        $header = new Header($token);

        $this->assertEquals([
            'Authorization' => 'Bearer '. $bearerToken
        ], $header->header());

    }

}