<?php


namespace Betalabs\Engine\Cache;


use Betalabs\Engine\Cache\Services\Redis;
use Betalabs\Engine\Configs\Cache;
use Symfony\Component\Cache\Simple\AbstractCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\RedisCache;

class Factory
{
    /**
     * @var \Betalabs\Engine\Configs\Cache
     */
    private $cacheConfig;

    /**
     * Factory constructor.
     *
     * @param \Betalabs\Engine\Configs\Cache $cacheConfig
     */
    public function __construct(Cache $cacheConfig)
    {
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * Return a cache service instance.
     *
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function create(): AbstractCache
    {
        $driver = $this->cacheConfig->driver();

        switch ($driver) {
            case 'redis':
                Redis::setCacheConfig($this->cacheConfig);
                return new RedisCache(Redis::get());
            default:
                return new FilesystemCache();
        }
    }
}