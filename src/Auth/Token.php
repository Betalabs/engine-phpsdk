<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;

class Token
{
    /** @var \Betalabs\Engine\Configs\Auth */
    protected $config;
    /** @var string */
    protected static $accessToken;

    /**
     * Token constructor.
     *
     * @param \Betalabs\Engine\Configs\Auth $config
     */
    public function __construct(Auth $config)
    {
        $this->config = $config;
    }

    /**
     * Inform the bearer token information
     *
     * @param string $accessToken
     *
     * @return $this
     */
    public function informToken($accessToken)
    {
        self::$accessToken = $accessToken;

        return $this;
    }

    /**
     * Retrieve access token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function retrieveToken()
    {
        $this->retrieveConfig();

        if (is_null(self::$accessToken)) {
            throw new UnauthorizedException(
                'Token not informed. Impossible to authenticate'
            );
        }

        return self::$accessToken;
    }

    /**
     * Retrieve data from configuration
     *
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    protected function retrieveConfig()
    {
        try {
            self::$accessToken = $this->config->accessToken();
        } catch (AuthNotDefinedException | AuthInternalNotDefinedException $e) {
            self::$accessToken = self::$accessToken ?? null;
        }
    }

    /**
     * Clear tokens from memory
     */
    public function clearToken()
    {
        self::$accessToken = null;
    }

    /**
     * @return string
     */
    public static function getAccessToken()
    {
        return self::$accessToken;
    }

}