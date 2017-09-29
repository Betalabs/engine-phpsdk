<?php

namespace Betalabs\Engine\Tests\Migration;

use Betalabs\Engine\Configs\MigrationProvider;
use Betalabs\Engine\Configs\Exceptions\MigrationProviderNotDefinedException;
use Betalabs\Engine\Migration\Boot;
use Betalabs\Engine\Requests\BootResponse;
use Betalabs\Engine\MigrationProvider as MigrationProviderInterface;
use Betalabs\Engine\Tests\TestCase;

class BootTest extends TestCase
{

    public function testRunCallAllMethods()
    {

        $bootResponse = \Mockery::mock(BootResponse::class);
        $bootResponse->shouldReceive('formatResponse')
            ->once()
            ->andReturn('success');

        $migrationProviderInterface = \Mockery::mock(MigrationProviderInterface::class);
        $migrationProviderInterface->shouldReceive('run')
            ->once()
            ->andReturn($bootResponse);

        $migrationProvider = \Mockery::mock(MigrationProvider::class);
        $migrationProvider->shouldReceive('migrationProvider')
            ->once()
            ->andReturn($migrationProviderInterface);

        $boot = new Boot($migrationProvider);

        $this->assertEquals(
            'success',
            $boot->run()
        );

    }

    public function testAppropriateReturnWhenExceptionIsThrown()
    {

        $migrationProvider = \Mockery::mock(MigrationProvider::class);
        $migrationProvider->shouldReceive('migrationProvider')
            ->once()
            ->andThrow(MigrationProviderNotDefinedException::class);

        $boot = new Boot($migrationProvider);

        $this->assertEquals(
            [
                'success' => true,
                'message' => 'Migration configuration not informed'
            ],
            $boot->run()
        );

    }

}