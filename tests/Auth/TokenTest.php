<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Client;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Requests\Methods\Post;
use Carbon\Carbon;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

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

    }

    public function testNonExpiredTokenReturnsItself()
    {

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $token = new Token($auth);

        $accessToken = 'access-token-hash';

        $token->informToken($accessToken, 'refresh-token', Carbon::now()->addHour());

        $this->assertEquals(
            $accessToken,
            $token->retrieveToken()
        );

    }

    public function testExpiredTokenWithoutRefreshTokenThrowsException()
    {

        $this->expectException(TokenExpiredException::class);

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $token = new Token($auth);

        $token->informToken('bearer-token-hash', null, Carbon::now()->subMinute());

        $token->retrieveToken();

    }

    public function testExpiredTokenWithRefreshTokenTriesToRefreshToken()
    {

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(Post::class)
            ->andReturn($this->mockPost());

        $container->shouldReceive('get')
            ->with(Client::class)
            ->andReturn($this->mockClient());

        Carbon::setTestNow();

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $token = new Token($auth);
        $token->setDiContainer($container);

        $token->informToken('access-token-hash', 'refresh-token', Carbon::now()->subMinute());

        $token->retrieveToken();

        $this->assertEquals(
            'new-access-token',
            Token::getAccessToken()
        );

        $this->assertEquals(
            'new-refresh-token',
            Token::getRefreshToken()
        );

        $this->assertEquals(
            Carbon::now()->addMinute()->timestamp,
            Token::getExpiresAt()->timestamp
        );

    }

    public function testTokenDefinedInConfigIsReturnedOverOthers()
    {

        $accessToken = 'config-access-token-hash';
        $refreshToken = 'config-refresh-token-hash';

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andReturn($accessToken);
        $auth->shouldReceive('refreshToken')
            ->andReturn($refreshToken);
        $auth->shouldReceive('expiresAt')
            ->andReturn(Carbon::now()->addMinute()->timestamp);

        $token = new Token($auth);

        $token->informToken('access-token-hash', 'refresh-token', Carbon::now()->addMinute());

        $this->assertEquals(
            $accessToken,
            $token->retrieveToken()
        );

        $this->assertEquals(
            $refreshToken,
            Token::getRefreshToken()
        );

    }

    protected function mockPost()
    {
        $post = \Mockery::mock(Post::class);
        $post->shouldReceive('setEndpointSuffix')
            ->with(null)
            ->andReturn($post);
        $post->shouldReceive('send')
            ->with(
                'oauth/token',
                [
                    'grant_type' => 'refresh_token',
                    'client_id' => '12',
                    'client_secret' => 'client-secret-hash',
                    'scope' => '*',
                    'refresh_token' => 'refresh-token'
                ]
            )
            ->andReturn((object)[
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_in' => 60,
            ]);
        return $post;
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function mockClient()
    {
        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('id')
            ->once()
            ->andReturn(12);
        $client->shouldReceive('secret')
            ->once()
            ->andReturn('client-secret-hash');
        return $client;
    }

}