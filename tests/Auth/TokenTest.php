<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Tests\TestCase;

class TokenTest extends TestCase
{
    public function testExceptionWhenNoTokenIsInformed()
    {

        $this->expectException(UnauthorizedException::class);

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $token = new Token($auth);

        $token->retrieveToken();

        $token->clearToken();

    }

    public function testTokenDefinedInConfigIsReturnedOverOthers()
    {

        $accessToken = 'config-access-token-hash';

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andReturn($accessToken);

        $token = new Token($auth);

        $token->informToken('access-token-hash');

        $this->assertEquals(
            $accessToken,
            $token->retrieveToken()
        );

        $token->clearToken();
    }
}