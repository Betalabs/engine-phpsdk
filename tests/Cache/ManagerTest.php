<?php

namespace Betalabs\Engine\Tests\Cache;

use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Cache\Factory;
use Betalabs\Engine\Cache\Manager;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Cache;
use Betalabs\Engine\Tests\TestCase;
use Carbon\Carbon;

class ManagerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Credentials::$identifier = $this->getName();
        Credentials::$id = 123;
        Credentials::$secret = 'lalala123';
        Credentials::$password = 'pass';
        Credentials::$username = 'username';
        Credentials::$apiUri = 'engine.local';

        Carbon::setTestNow(Carbon::now());
        $this->prepareTokens();
    }

    public function prepareTokens()
    {
        $authConfig = $this->getMockBuilder(Auth::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheManager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $token = new Token($authConfig, $cacheManager);
        $token->informToken(
            'accessToken',
            'refreshToken',
            Carbon::now()->addMinute()
        );
    }

    public function testCanSetCache()
    {
        $cacheConfig = $this->mockCacheConfig();

        $cacheFactory = new Factory($cacheConfig);
        $manager = new Manager($cacheFactory);
        $inserted = $manager->insert();

        $this->assertTrue($inserted);
    }

    public function testCanRetrieveCache()
    {
        $cacheConfig = $this->mockCacheConfig();

        $cacheFactory = new Factory($cacheConfig);
        $manager = new Manager($cacheFactory);
        $manager->insert();
        $cached = $manager->retrieve();

        $this->assertEquals('accessToken', $cached->accessToken);
        $this->assertEquals('refreshToken', $cached->refreshToken);
        $this->assertEquals(
            Carbon::now()->addMinute()->timestamp,
            $cached->expiresAt
        );
    }

    public function testCanDeleteCache()
    {
        $cacheConfig = $this->mockCacheConfig();

        $cacheFactory = new Factory($cacheConfig);
        $manager = new Manager($cacheFactory);
        $manager->insert();

        $this->assertTrue($manager->delete());
    }

    private function mockCacheConfig()
    {
        $cacheConfig = $this->getMockBuilder(Cache::class)
            ->disableOriginalConstructor()
            ->setMethods(['driver', 'host', 'port', 'password'])
            ->getMock();
        $cacheConfig->expects($this->atMost(1))
            ->method('driver')
            ->willReturn('filesystem');
        $cacheConfig->expects($this->never())
            ->method('host')
            ->willReturn('localhost');
        $cacheConfig->expects($this->never())
            ->method('port')
            ->willReturn('1234');
        $cacheConfig->expects($this->never())
            ->method('password')
            ->willReturn('');
        return $cacheConfig;
    }

    public function tearDown()
    {
        parent::tearDown();

        Credentials::clear();
    }
}
