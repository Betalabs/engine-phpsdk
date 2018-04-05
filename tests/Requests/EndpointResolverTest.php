<?php

namespace Betalabs\Engine\Tests\Requests;

use Betalabs\Engine\Auth\Credentials;
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

    public function testEndpointWhenEnvironmentNodeDoesNotExistAndEndpointIsNotInformed()
    {

        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $environment->shouldReceive('environment')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('http://engine.local', $endpoint->endpoint());

    }

    public function testEndpointWhenEnvironmentNodeDoesNotExistAndEndpointIsInformed()
    {

        $environment = \Mockery::mock(Environment::class);
        $environment->shouldReceive('endpoint')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        $environment->shouldReceive('environment')
            ->andThrow(EnvironmentInternalNodeNotDefinedException::class);

        EndpointResolver::setEndpoint('http://new.endpoint');

        $endpoint = new EndpointResolver($environment);
        $this->assertEquals('http://new.endpoint', $endpoint->endpoint());

    }

    public function testEndpointProvidedByCredentials()
    {
        Credentials::$identifier = 'Engine-PhpSDK';
        Credentials::$username = 'teste@teste.com';
        Credentials::$password = '123456';
        Credentials::$apiUri = 'http://php-sdk.engine';
        Credentials::$id = 1;
        Credentials::$secret = '123abc';

        $environment = \Mockery::mock(Environment::class);

        $endpoint = new EndpointResolver($environment);

        $this->assertEquals('http://php-sdk.engine', $endpoint->endpoint());
    }

    public function tearDown()
    {
        parent::tearDown();

        Credentials::clear();
    }
}