<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\DatabaseProvider;
use Betalabs\Engine\Configs\Exceptions\DatabaseClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\DatabaseProviderNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class DatabaseProviderTest extends TestCase
{

    public function testDatabaseProviderExistsWithoutPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'databaseProvider' => (object)[
                    'class' => 'Database\Provider'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Database\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Database\Provider')
            ->andReturn('success');

        $config = new DatabaseProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->databaseProvider()
        );

    }

    public function testDatabaseProviderExistsWithPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'databaseProvider' => (object)[
                    'class' => 'Database\Provider',
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
            ->with('Database\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Database\Provider')
            ->andReturn('success');

        $config = new DatabaseProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->databaseProvider()
        );

    }

    public function testDatabaseProviderNodeIsNotInformed()
    {

        $this->expectException(DatabaseProviderNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->once()
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);
        $container = \Mockery::mock(Container::class);

        $config = new DatabaseProvider($reader, $helper, $container);

        $config->databaseProvider();

    }

    public function testDatabaseProviderClassDoesNotExist()
    {

        $this->expectException(DatabaseClassDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'databaseProvider' => (object)[
                    'class' => 'Database\Provider',
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Database\Provider')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $config = new DatabaseProvider($reader, $helper, $container);

        $config->databaseProvider();

    }

}