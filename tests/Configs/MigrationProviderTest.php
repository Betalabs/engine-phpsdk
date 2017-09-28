<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\MigrationProvider;
use Betalabs\Engine\Configs\Exceptions\MigrationClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\MigrationProviderNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class MigrationProviderTest extends TestCase
{

    public function testMigrationProviderExistsWithoutPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'migrationProvider' => (object)[
                    'class' => 'Migration\Provider'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Migration\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Migration\Provider')
            ->andReturn('success');

        $config = new MigrationProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->migrationProvider()
        );

    }

    public function testMigrationProviderExistsWithPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'migrationProvider' => (object)[
                    'class' => 'Migration\Provider',
                    'path' => 'file/path.php'
                ]
            ]);
        $reader->shouldReceive('getRootPath')
            ->once()
            ->andReturn('root');

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('fileExists')
            ->with('root/file/path.php')
            ->andReturn(true);
        $helper->shouldReceive('requireFileOnce')
            ->with('root/file/path.php')
            ->andReturn(true);
        $helper->shouldReceive('classExists')
            ->with('Migration\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Migration\Provider')
            ->andReturn('success');

        $config = new MigrationProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->migrationProvider()
        );

    }

    public function testMigrationProviderNodeIsNotInformed()
    {

        $this->expectException(MigrationProviderNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->once()
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);
        $container = \Mockery::mock(Container::class);

        $config = new MigrationProvider($reader, $helper, $container);

        $config->migrationProvider();

    }

    public function testMigrationProviderClassDoesNotExist()
    {

        $this->expectException(MigrationClassDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'migrationProvider' => (object)[
                    'class' => 'Migration\Provider',
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Migration\Provider')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $config = new MigrationProvider($reader, $helper, $container);

        $config->migrationProvider();

    }

}