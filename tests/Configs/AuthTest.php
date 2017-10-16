<?php

namespace Betalabs\Engine\Tests\Configs;

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

    public function testRefreshTokenNodeDoesNotExist()
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
        $environment->refreshToken();

    }

    public function testRefreshTokenReturnToken()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'auth' => (object) [
                    'refreshToken' => 'refresh-token-hash'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Auth($reader, $helper, $container);
        $this->assertEquals('refresh-token-hash', $environment->refreshToken());

    }

    public function testExpiresAtNodeDoesNotExist()
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
        $environment->expiresAt();

    }

    public function testExpiresAtReturnNumber()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'auth' => (object) [
                    'expiresAt' => 999999
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Auth($reader, $helper, $container);
        $this->assertEquals(999999, $environment->expiresAt());

    }

}