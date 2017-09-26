<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Token;
use Carbon\Carbon;
use Betalabs\Engine\Tests\TestCase;

class TokenTest extends TestCase
{

    public function testExceptionWhenNoTokenIsInformed()
    {

        $this->expectException(UnauthorizedException::class);

        $token = new Token();

        $token->retrieveToken();

    }

    public function testNonExpiredTokenReturnsItself()
    {

        $token = new Token();

        $bearerToken = 'bearer-token-hash';

        $token->informToken($bearerToken, 'refresh-token', Carbon::now()->addHour());

        $this->assertEquals(
            $bearerToken,
            $token->retrieveToken()
        );

    }

    public function testExpiredTokenWithoutRefreshTokenThrowsException()
    {

        $this->expectException(TokenExpiredException::class);

        $token = new Token();

        $expiredBearerToken = 'bearer-token-hash';

        $token->informToken($expiredBearerToken, null, Carbon::now()->subMinute());

        $token->retrieveToken();

    }

}