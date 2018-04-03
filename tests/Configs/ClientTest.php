<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Configs\Client;
use Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class ClientTest extends TestCase
{

    public function testExceptionIsThrownWhenClientNodeDoesNotExist()
    {

        $this->expectException(ClientNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $client = new Client($reader, $helper, $container);

        $client->id();

    }

    public function testExceptionIsThrownWhenIdPropertyDoesNotExist()
    {

        $this->expectException(PropertyNotFoundException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'client' => (object) []
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $client = new Client($reader, $helper, $container);

        $client->id();

    }

    public function testExceptionIsThrownWhenSecretPropertyDoesNotExist()
    {

        $this->expectException(PropertyNotFoundException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'client' => (object) []
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $client = new Client($reader, $helper, $container);

        $client->secret();

    }

    public function testPropertiesAreReturningFromFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'client' => (object) [
                    'id' => 12,
                    'secret' => 'secret-hash'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $client = new Client($reader, $helper, $container);

        $this->assertEquals(12, $client->id());
        $this->assertEquals('secret-hash', $client->secret());

    }

}