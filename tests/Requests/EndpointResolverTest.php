<?php

namespace Betalabs\Engine\Tests\Requests;

use Betalabs\Engine\Configs\Environment;
use Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException;
use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Tests\TestCase;

class EndpointResolverTest extends TestCase
{
    
    public function testEndpointProvidedByEndpointNode()
    {
        
        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andReturn('http://custom.endpoint');

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('http://custom.endpoint', $endpoint->endpoint());

    }

    public function testSandboxEndpointByEnvironmentNode()
    {

        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $environment->shouldReceive('environment')
            ->andReturn('Sandbox');

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('https://api.sandbox.betalabs.net', $endpoint->endpoint());

    }

    public function testProductionEndpointByEnvironmentNode()
    {

        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $environment->shouldReceive('environment')
            ->andReturn('Production');

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('http://engine.local', $endpoint->endpoint());

    }

    public function testEndpointWhenEnvironmentNodeDoesNotExist()
    {

        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $environment->shouldReceive('environment')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('http://engine.local', $endpoint->endpoint());

    }

}