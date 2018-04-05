<?php


namespace Betalabs\Engine\Cache\Services;


use Betalabs\Engine\Configs\Cache;

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
     * @return \Redis
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public static function get(): \Redis
    {
        if (self::$conn === null) {
            $redis = new \Redis();
            $redis->connect(
                self::$cacheConfig->host(),
                self::$cacheConfig->port()
            );

            self::$conn = $redis;
        }

        return self::$conn;
    }
}