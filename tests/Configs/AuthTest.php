<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class AuthTest extends TestCase
{

    public function testAuthNodeDoesNotExist()
    {

        $this->expectException(AuthNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Auth($reader, $helper, $container);
        $environment->accessToken();

    }

    public function testAccessTokenNodeDoesNotExist()
    {

        $this->expectException(AuthInternalNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'auth' => (object) []
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Auth($reader, $helper, $container);
        $environment->accessToken();

    }

    public function testAccessTokenReturnToken()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'auth' => (object) [
                    'accessToken' => 'access-token-hash'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Auth($reader, $helper, $container);
        $this->assertEquals('access-token-hash', $environment->accessToken());

    }

    public function testAccessTokenIsNotInformed()
    {
        $this->expectException(UnauthorizedException::class);

        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $testAccessToken = 'test-access-token';

        $token = new Token($auth);
        $this->assertEquals($testAccessToken, $token->retrieveToken());
    }

    public function testAccessTokenWhenAuthNodeDoesNotExistAndAccessTokenIsInformed()
    {
        $auth = \Mockery::mock(Auth::class);
        $auth->shouldReceive('accessToken')
            ->andThrow(AuthInternalNotDefinedException::class);

        $testAccessToken = 'test-access-token';

        $token = new Token($auth);
        $token->informToken($testAccessToken);
        $this->assertEquals($testAccessToken, $token->retrieveToken());
    }
}