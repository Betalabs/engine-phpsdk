<?php

namespace Betalabs\Engine\Tests\Genesis;

use Betalabs\Engine\Configs\Exceptions\GenesisProviderNotDefinedException;
use Betalabs\Engine\Configs\GenesisProvider;
use Betalabs\Engine\Genesis\Boot;
use Betalabs\Engine\GenesisProvider as GenesisProviderInterface;
use Betalabs\Engine\Requests\BootResponse;
use Betalabs\Engine\Tests\TestCase;

class BootTest extends TestCase
{

    public function testRunCallAllMethods()
    {

        $bootResponse = \Mockery::mock(BootResponse::class);
        $bootResponse->shouldReceive('formatResponse')
            ->once()
            ->andReturn('success');

        $genesisProviderInterface = \Mockery::mock(GenesisProviderInterface::class);
        $genesisProviderInterface->shouldReceive('run')
            ->once()
            ->andReturn($bootResponse);

        $migrationProvider = \Mockery::mock(GenesisProvider::class);
        $migrationProvider->shouldReceive('genesisProvider')
            ->once()
            ->andReturn($genesisProviderInterface);

        $boot = new Boot($migrationProvider);

        $this->assertEquals(
            'success',
            $boot->run()
        );

    }

    public function testAppropriateReturnWhenExceptionIsThrown()
    {

        $genesisProvider = \Mockery::mock(GenesisProvider::class);
        $genesisProvider->shouldReceive('genesisProvider')
            ->once()
            ->andThrow(GenesisProviderNotDefinedException::class);

        $boot = new Boot($genesisProvider);

        $this->assertEquals(
            [
                'success' => true,
                'message' => 'Genesis configuration not informed'
            ],
            $boot->run()
        );

    }

}