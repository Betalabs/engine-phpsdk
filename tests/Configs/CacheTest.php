<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Cache;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Tests\TestCase;
use DI\Container;

class CacheTest extends TestCase
{
    public function testCanRetrieveProperties()
    {
        $cache = new \stdClass();
        $cache->driver = 'test';
        $cache->host = 'test';
        $cache->port = 'test';
        $cache->password = 'test';

        $response = new \stdClass();
        $response->cache = $cache;

        $reader = \Mockery::mock(Reader::class);
        $reader->shouldReceive('load')
            ->andReturn($response);

        $helper = \Mockery::mock(Helper::class);
        $container = \Mockery::mock(Container::class);

        $cacheConfig = new Cache($reader, $helper, $container);
        $this->assertNotEmpty($cacheConfig->driver());
        $this->assertNotEmpty($cacheConfig->host());
        $this->assertNotEmpty($cacheConfig->port());
        $this->assertNotEmpty($cacheConfig->password());
    }
}
