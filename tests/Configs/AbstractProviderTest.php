<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Exceptions\FileDoesNotExistException;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Configs\RouteProvider as RouteProviderConfig;
use Betalabs\Engine\Tests\Fixtures\FakeDependency;
use Betalabs\Engine\Tests\Fixtures\FakeRouteProvider;
use Betalabs\Engine\Tests\Fixtures\FakeRouteProviderWithDependency;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

/**
 * Exercises AbstractProvider through RouteProvider with a real Helper and a
 * real PHP-DI container (only the XML reader is mocked)
 */
class AbstractProviderTest extends TestCase
{

    public function testProviderWithoutContainerBuildsDefaultContainer()
    {

        $config = new RouteProviderConfig(
            $this->mockReader(FakeRouteProvider::class),
            new Helper()
        );

        $this->assertInstanceOf(
            FakeRouteProvider::class,
            $config->routeProvider()
        );

    }

    public function testDefaultContainerAutowiresConstructorDependencies()
    {

        $config = new RouteProviderConfig(
            $this->mockReader(FakeRouteProviderWithDependency::class),
            new Helper()
        );

        $provider = $config->routeProvider();

        $this->assertInstanceOf(FakeRouteProviderWithDependency::class, $provider);
        $this->assertInstanceOf(FakeDependency::class, $provider->dependency);

    }

    public function testProviderUsesGivenContainer()
    {

        $instance = new FakeRouteProvider();

        $container = new Container();
        $container->set(FakeRouteProvider::class, $instance);

        $config = new RouteProviderConfig(
            $this->mockReader(FakeRouteProvider::class),
            new Helper(),
            $container
        );

        $this->assertSame($instance, $config->routeProvider());

    }

    public function testProviderRequiresClassFileFromConfiguredPath()
    {

        $config = new RouteProviderConfig(
            $this->mockReader(
                'BetalabsEngineTestsRequired\RequiredRouteProvider',
                'files/RequiredRouteProvider.php',
                __DIR__ . '/../Fixtures'
            ),
            new Helper()
        );

        $this->assertInstanceOf(
            'BetalabsEngineTestsRequired\RequiredRouteProvider',
            $config->routeProvider()
        );

    }

    public function testProviderWithMissingClassFileThrowsException()
    {

        $config = new RouteProviderConfig(
            $this->mockReader(
                'BetalabsEngineTestsRequired\DoesNotExist',
                'files/does-not-exist.php',
                __DIR__ . '/../Fixtures'
            ),
            new Helper()
        );

        $this->expectException(FileDoesNotExistException::class);

        $config->routeProvider();

    }

    protected function mockReader($class, $path = '', $rootPath = '/path/to/root')
    {

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn((object) [
                'routeProvider' => (object) [
                    'path' => $path,
                    'class' => $class
                ]
            ]);

        $reader->shouldReceive('getRootPath')
            ->andReturn($rootPath);

        return $reader;

    }

}
