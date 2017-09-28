<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Exceptions\PermissionClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\PermissionProviderNotDefinedException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\PermissionProvider;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class PermissionProviderTest extends TestCase
{

    public function testPermissionProviderExistsWithoutPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'permissionProvider' => (object)[
                    'class' => 'Permission\Provider'
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Permission\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Permission\Provider')
            ->andReturn('success');

        $config = new PermissionProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->permissionProvider()
        );

    }

    public function testPermissionProviderExistsWithPathInConfigFile()
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'permissionProvider' => (object)[
                    'class' => 'Permission\Provider',
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
            ->with('Permission\Provider')
            ->andReturn(true);

        $container = \Mockery::mock(Container::class);
        $container->shouldReceive('get')
            ->with('Permission\Provider')
            ->andReturn('success');

        $config = new PermissionProvider($reader, $helper, $container);

        $this->assertEquals(
            'success',
            $config->permissionProvider()
        );

    }

    public function testPermissionProviderNodeIsNotInformed()
    {

        $this->expectException(PermissionProviderNotDefinedException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->once()
            ->andReturn((object) []);

        $helper = \Mockery::mock(Helper::class);
        $container = \Mockery::mock(Container::class);

        $config = new PermissionProvider($reader, $helper, $container);

        $config->permissionProvider();

    }

    public function testPermissionProviderClassDoesNotExist()
    {

        $this->expectException(PermissionClassDoesNotExistException::class);

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->times(2)
            ->andReturn((object) [
                'permissionProvider' => (object)[
                    'class' => 'Permission\Provider',
                ]
            ]);

        $helper = \Mockery::mock(Helper::class);
        $helper->shouldReceive('classExists')
            ->with('Permission\Provider')
            ->andReturn(false);

        $container = \Mockery::mock(Container::class);

        $config = new PermissionProvider($reader, $helper, $container);

        $config->permissionProvider();

    }


}