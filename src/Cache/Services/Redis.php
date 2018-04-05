<?php


namespace Betalabs\Engine\Cache\Services;


use Betalabs\Engine\Configs\Cache;
use Predis\Client;

class Redis
{
    /**
     * @var \Redis
     */
    private static $conn = null;
    /**
     * @var \Betalabs\Engine\Configs\Cache
     */
    private static $cacheConfig = null;

    /**
     * Sets the cacheConfig property.
     *
     * @param \Betalabs\Engine\Configs\Cache $cacheConfig
     */
    public static function setCacheConfig(Cache $cacheConfig): void
    {
        self::$cacheConfig = $cacheConfig;
    }

    /**
     * Return a Redis client
     *
     * @return \Predis\Client
     */
    public static function get(): Client
    {
        if (self::$conn === null) {
            $redis = new Client([
                'host' => self::$cacheConfig->host(),
                'port' => self::$cacheConfig->port(),
                'password' => self::$cacheConfig->password()
            ]);

            self::$conn = $redis;
        }

        return self::$conn;
    }
}