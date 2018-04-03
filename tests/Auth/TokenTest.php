<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Credentials;
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
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class TokenTest extends TestCase
{
    /**
     * @var \Betalabs\Engine\Database\Token
     */
    private $tokenModel;

    protected function setUp()
    {
        parent::setUp();

        $this->tokenModel = new \Betalabs\Engine\Database\Token();
    }

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

        $token->clearToken();

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
        $token->clearToken();

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

        $token->clearToken();
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

        $token->clearToken();
    }

    protected function mockPost()
    {
        $post = \Mockery::mock(Post::class);
        $post->shouldReceive('setEndpointSuffix')
            ->with(null)
            ->andReturn($post);
        $post->shouldReceive('mustNotAuthorize')
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
        $client->shouldReceive('username')
            ->andReturn('username');
        $client->shouldReceive('password')
            ->andReturn('password');
        return $client;
    }

    public function testRetrieveNewTokenWithDefinedCredentials()
    {
        Carbon::setTestNow();

        Credentials::$apiUri = 'engine.local';
        Credentials::$identifier = time();
        Credentials::$username = 'token-test';
        Credentials::$password = 'test-token';
        Credentials::$id = 1;
        Credentials::$secret = 'client-secret';

        $post = \Mockery::mock(Post::class);
        $post->shouldReceive('setEndpointSuffix')
            ->with(null)
            ->andReturn($post);
        $post->shouldReceive('mustNotAuthorize')
            ->andReturn($post);
        $post->shouldReceive('send')
            ->with(
                'oauth/token',
                [
                    'grant_type' => 'password',
                    'username' => 'username',
                    'password' => 'password',
                    'client_id' => 12,
                    'client_secret' => 'client-secret-hash',
                    'scope' => '*',
                ]
            )
            ->andReturn((object)[
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_in' => 60,
            ]);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(Post::class)
            ->andReturn($post);
        $container->shouldReceive('get')
            ->with(Client::class)
            ->andReturn($this->mockClient());

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $token = new Token($auth);
        $token->setDiContainer($container);

        $accessToken = $token->retrieveToken();
        $tokens = $this->tokenModel->first();

        $this->assertEquals('new-access-token', $accessToken);
        $this->assertEquals('new-access-token', $tokens->access_token);
        $token->clearToken();
    }

    public function testTokenWithDefinedCredentialsShouldFillDatabase()
    {
        Carbon::setTestNow();

        Credentials::$apiUri = 'engine.local';
        Credentials::$identifier = time();
        Credentials::$username = 'token-test';
        Credentials::$password = 'test-token';
        Credentials::$id = 1;
        Credentials::$secret = 'client-secret';

        $accessToken = 'access-token-hash';
        $refreshToken = 'refresh-token';

        $auth = \Mockery::mock(Auth::class);
        $token = new Token($auth);
        $token->informToken($accessToken, $refreshToken, Carbon::now()->addMinute());

        $tokens = $this->tokenModel->first();

        $this->assertEquals($accessToken, $token->retrieveToken());
        $this->assertEquals($accessToken, $tokens->access_token);
        $token->clearToken();
    }

    public function testExpiredTokenWithRefreshTokenTriesToRefreshTokenUsingCredentials()
    {
        Carbon::setTestNow();

        Credentials::$apiUri = 'engine.local';
        Credentials::$identifier = time();
        Credentials::$username = 'token-test';
        Credentials::$password = 'test-token';
        Credentials::$id = 1;
        Credentials::$secret = 'client-secret';

        $accessToken = 'access-token-hash';
        $refreshToken = 'refresh-token';

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(Post::class)
            ->andReturn($this->mockPost());

        $container->shouldReceive('get')
            ->with(Client::class)
            ->andReturn($this->mockClient());

        $auth = \Mockery::mock(Auth::class);

        $token = new Token($auth);
        $token->setDiContainer($container);
        $token->informToken($accessToken, $refreshToken, new Carbon('last week'));

        $newAccessToken = $token->retrieveToken();
        $tokens = $this->tokenModel->first();

        $this->assertEquals('new-access-token', $newAccessToken);
        $this->assertEquals('new-access-token', $tokens->access_token);
        $token->clearToken();
    }

    public function testExpiredRefreshTokenTriesToRetrieveNewTokensUsingCredentials()
    {
        Carbon::setTestNow();

        Credentials::$apiUri = 'engine.local';
        Credentials::$identifier = time();
        Credentials::$username = 'token-test';
        Credentials::$password = 'test-token';
        Credentials::$id = 1;
        Credentials::$secret = 'client-secret';

        $exception = $this->getMockBuilder(ClientException::class)
            ->setConstructorArgs([
                'Error',
                new Request('GET', 'test'),
                new Response(401)
            ])
            ->setMethods(['getCode'])
            ->getMock();
        $exception->expects($this->never())
            ->method('getcode')
            ->willReturn(401);

        $response = new \stdClass();
        $response->access_token = 'new-access-token';
        $response->refresh_token = 'new-refresh-token';
        $response->expires_in = 60;

        $post = $this->getMockBuilder(Post::class)
            ->disableOriginalConstructor()
            ->setMethods(['setEndpointSuffix', 'mustNotAuthorize', 'send'])
            ->getMock();
        $post->method('setEndpointSuffix')->willReturn($post);
        $post->method('mustNotAuthorize')->willReturn($post);
        $post->expects($this->at(0))
            ->method('send')
            ->willThrowException($exception);
        $post->expects($this->at(2))
            ->method('send')
            ->willReturn($response);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with(Post::class)
            ->andReturn($post);
        $container->shouldReceive('get')
            ->with(Client::class)
            ->andReturn($this->mockClient());

        $auth = \Mockery::mock(Auth::class);

        $token = new Token($auth);
        $token->informToken(
            'refresh-test-access-token',
            'refresh-test-refresh-token',
            new Carbon('last week')
        );
        $token->setDiContainer($container);
        $token->clearToken();

        $newAccessToken = $token->retrieveToken();
        $tokens = $this->tokenModel->first();

        $this->assertEquals('new-access-token', $newAccessToken);
        $this->assertEquals('new-access-token', $tokens->access_token);
    }

    public function tearDown()
    {
        parent::tearDown();

        Credentials::clear();
    }
}