<?php


namespace Betalabs\Engine\Cache;


use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Auth\Token;
use Carbon\Carbon;
use Psr\SimpleCache\InvalidArgumentException;

class Manager
{

    /**
     * @var \Symfony\Component\Cache\Simple\AbstractCache
     */
    private static $cache;

    /**
     * Manager constructor.
     *
     * @param \Betalabs\Engine\Cache\Factory $cacheFactory
     *
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function __construct(Factory $cacheFactory)
    {
        if (self::$cache === null) {
            self::$cache = $cacheFactory->create();
        }
    }

    /**
     * Persist tokens in the cache.
     *
     * @return boolean
     */
    public function insert()
    {
        if (!Credentials::isValid()) {
            return false;
        }

        $expiresAt = Token::getExpiresAt();
        $lastYear = new Carbon('last year');

        if ($expiresAt < $lastYear) {
            $expiresAt = Carbon::now()->addSeconds($expiresAt->timestamp);
        }

        try {
            return self::$cache->set(Credentials::$identifier, serialize((object)[
                'accessToken' => Token::getAccessToken(),
                'refreshToken' => Token::getRefreshToken(),
                'expiresAt' => $expiresAt->timestamp,
            ]));
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Retrieve a value from the cache.
     *
     * @return mixed|null
     */
    public function retrieve()
    {
        if (!Credentials::isValid()) {
            return null;
        }

        try {
            return unserialize(self::$cache->get(Credentials::$identifier));
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Delete a value from the cache.
     *
     * @return bool
     */
    public function delete()
    {
        if (!Credentials::isValid()) {
            return false;
        }

        return self::$cache->delete(Credentials::$identifier);
    }
}