<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Header;
use Betalabs\Engine\Auth\Token;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{

    public function testHeaderDoesNotIncludeAuthorizationWhenMustNotAuthorize()
    {

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $header->setBearerToken('bearer-token-hash');
        $header->mustNotAuthorize();

        $this->assertEmpty($header->header());

    }

    public function testHeaderIncludeAuthorizationWhenMustAuthorize()
    {

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $bearerToken = 'bearer-token-hash';
        $header->setBearerToken($bearerToken);

        $this->assertEquals([
            'Authorization' => 'Bearer '. $bearerToken
        ], $header->header());

    }

    public function testExceptionWhenNoTokenEmailOrPasswordIsInformed()
    {
        
        $this->expectException(UnauthorizedException::class);

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $header->retrieveToken();

    }

}