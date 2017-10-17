<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Environment;
use Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\EnvironmentNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class EnvironmentTest extends TestCase
{

    public function testEnvironmentNodeDoesNotExistOnEndpointCall()
    {

        $this->expectException(EnvironmentNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Environment($reader, $helper, $container);
        $environment->endpoint();

    }

    public function testEnvironmentNodeDoesNotExistOnEnvironmentCall()
    {

        $this->expectException(EnvironmentInternalNodeNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) ['environment' => (object) []]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Environment($reader, $helper, $container);
        $environment->environment();

    }

    public function testEndpointNodeDoesNotExistOnEnvironmentCall()
    {

        $this->expectException(EnvironmentInternalNodeNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) ['environment' => (object) []]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Environment($reader, $helper, $container);
        $environment->endpoint();

    }

    public function testEnvironmentIsReturned()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'environment' => (object) [
                    'env' => 'Env name'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Environment($reader, $helper, $container);
        $this->assertEquals('Env name', $environment->environment());

    }

    public function testEndpointIsReturned()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'environment' => (object) [
                    'endpoint' => 'http://env.test'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);

        $container = \Mockery::mock(Container::class);

        $environment = new Environment($reader, $helper, $container);
        $this->assertEquals('http://env.test', $environment->endpoint());

    }

}