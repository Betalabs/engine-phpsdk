<?php

namespace Betalabs\Engine\Tests\Database;

use Betalabs\Engine\Configs\DatabaseProvider;
use Betalabs\Engine\Configs\Exceptions\DatabaseProviderNotDefinedException;
use Betalabs\Engine\Database\Boot;
use Betalabs\Engine\Database\BootResponse;
use Betalabs\Engine\DatabaseProvider as DatabaseProviderInterface;
use Betalabs\Engine\Tests\TestCase;

class BootTest extends TestCase
{

    public function testRunCallAllMethods()
    {

        $bootResponse = \Mockery::mock(BootResponse::class);
        $bootResponse->shouldReceive('formatResponse')
            ->once()
            ->andReturn('success');

        $databaseProviderInterface = \Mockery::mock(DatabaseProviderInterface::class);
        $databaseProviderInterface->shouldReceive('run')
            ->once()
            ->andReturn($bootResponse);

        $databaseProvider = \Mockery::mock(DatabaseProvider::class);
        $databaseProvider->shouldReceive('databaseProvider')
            ->once()
            ->andReturn($databaseProviderInterface);

        $boot = new Boot($databaseProvider);

        $this->assertEquals(
            'success',
            $boot->run()
        );

    }

    public function testAppropriateReturnWhenExceptionIsThrown()
    {

        $databaseProvider = \Mockery::mock(DatabaseProvider::class);
        $databaseProvider->shouldReceive('databaseProvider')
            ->once()
            ->andThrow(DatabaseProviderNotDefinedException::class);

        $boot = new Boot($databaseProvider);

        $this->assertEquals(
            [
                'success' => true,
                'message' => 'Migration configuration not informed'
            ],
            $boot->run()
        );

    }

}