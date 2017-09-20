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
        $expiresAt = Carbon::now()->addHour();

        $token->informBearerToken($bearerToken, $expiresAt);

        $this->assertEquals(
            $bearerToken,
            $token->retrieveToken()
        );

    }

    public function testExpiredTokenThrowsException()
    {

        $this->expectException(TokenExpiredException::class);

        $token = new Token();

        $expiredBearerToken = 'bearer-token-hash';

        $expiresAt = Carbon::now()->subMinute();

        $token->informBearerToken($expiredBearerToken, $expiresAt);

        $token->retrieveToken();

    }

}