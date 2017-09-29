<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Exceptions\GenesisClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\GenesisProviderNotDefinedException;
use Betalabs\Engine\Configs\GenesisProvider;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class GenesisProviderTest extends TestCase
{

    public function testGenesisProviderExistsWithoutPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'genesisProvider' => (object)[
                    'class' => 'Genesis\Provider'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Genesis\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Genesis\Provider')
            ->andReturn('success');

        $config = new GenesisProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->genesisProvider()
        );

    }

    public function testGenesisProviderExistsWithPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'genesisProvider' => (object)[
                    'class' => 'Genesis\Provider',
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
            ->with('Genesis\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Genesis\Provider')
            ->andReturn('success');

        $config = new GenesisProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->genesisProvider()
        );

    }

    public function testGenesisProviderNodeIsNotInformed()
    {

        $this->expectException(GenesisProviderNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->once()
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);
        $container = \Mockery::mock(Container::class);

        $config = new GenesisProvider($reader, $helper, $container);

        $config->genesisProvider();

    }

    public function testGenesisProviderClassDoesNotExist()
    {

        $this->expectException(GenesisClassDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'genesisProvider' => (object)[
                    'class' => 'Genesis\Provider',
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Genesis\Provider')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $config = new GenesisProvider($reader, $helper, $container);

        $config->genesisProvider();

    }

}