<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\Client;
use Betalabs\Engine\Requests\Methods\Post;
use Carbon\Carbon;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

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

        $token = new Token();

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

        $token = new Token();
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
            Carbon::now()->addSeconds(60)->timestamp,
            Token::getExpiresAt()->timestamp
        );

    }

    protected function mockPost()
    {
        $post = \Mockery::mock(Post::class);
        $post->shouldReceive('setEndpointSufix')
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