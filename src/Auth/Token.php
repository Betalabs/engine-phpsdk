<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Client;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;
use Betalabs\Engine\Requests\Methods\Post;
use Carbon\Carbon;
use DI\Container;
use DI\ContainerBuilder;

class Token
{

    /** @var \Betalabs\Engine\Configs\Auth */
    protected $config;

    /** @var \DI\Container */
    protected $diContainer;

    /** @var  */
    protected static $endpoint;

    /** @var string */
    protected static $accessToken;

    /** @var string */
    protected static $refreshToken;

    /** @var \Carbon\Carbon */
    protected static $expiresAt;

    /**
     * Token constructor.
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
     * @param string $refreshToken
     * @param \Carbon\Carbon $expiresAt
     * @return $this
     */
    public function informToken($accessToken, $refreshToken, $expiresAt)
    {
        self::$accessToken = $accessToken;
        self::$refreshToken = $refreshToken;
        self::$expiresAt = $expiresAt;
        return $this;
    }

    /**
     * Retrieve access token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function retrieveToken()
    {
        $this->retrieveConfig();

        if(is_null(self::$accessToken)) {
            throw new UnauthorizedException(
                'Token not informed. Impossible to authenticate'
            );
        }

        if(Carbon::now()->subSeconds(15) > self::$expiresAt) {
            $this->refreshToken();
        }

        return self::$accessToken;
    }

    /**
     * Refresh access token
     *
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function refreshToken()
    {
        $this->retrieveConfig();

        if(is_null(self::$refreshToken)) {
            throw new TokenExpiredException('Token expired and there is no refresh token available');
        }

        $container = $this->diContainer();

        /** @var \Betalabs\Engine\Requests\Methods\Post $post */
        $post = $container->get(Post::class);

        /** @var \Betalabs\Engine\Configs\Client $client */
        $client = $container->get(Client::class);

        $response = $post->setEndpointSuffix(null)->send(
            'oauth/token',
            [
                'grant_type' => 'refresh_token',
                'client_id' => $client->id(),
                'client_secret' => $client->secret(),
                'scope' => '*',
                'refresh_token' => self::$refreshToken
            ]
        );

        $this->informToken(
            $response->access_token,
            $response->refresh_token,
            Carbon::now()->addSeconds($response->expires_in)
        );
    }

    /**
     * Retrieve data from configuration
     */
    protected function retrieveConfig()
    {
        try {
            self::$accessToken = $this->config->accessToken();
            self::$refreshToken = $this->config->refreshToken();
            self::$expiresAt = Carbon::createFromTimestamp((string) $this->config->expiresAt());
        } catch(AuthNotDefinedException | AuthInternalNotDefinedException $e) {
            self::$accessToken = self::$accessToken ?? null;
            self::$refreshToken = self::$refreshToken ?? null;
            self::$expiresAt = self::$expiresAt ?? null;
        }
    }

    /**
     * Return defined container or creates a new one
     *
     * @return \DI\Container
     */
    protected function diContainer()
    {
        if(is_null($this->diContainer)) {
            $this->diContainer = ContainerBuilder::buildDevContainer();
        }

        return $this->diContainer;
    }

    /**
     * Clear tokens from memory
     */
    public function clearToken()
    {
        self::$accessToken = null;
        self::$refreshToken = null;
        self::$expiresAt = null;
    }

    /**
     * @param \DI\Container $diContainer
     */
    public function setDiContainer(Container $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    /**
     * @return string
     */
    public static function getAccessToken()
    {
        return self::$accessToken;
    }

    /**
     * @return string
     */
    public static function getRefreshToken()
    {
        return self::$refreshToken;
    }

    /**
     * @return \Carbon\Carbon
     */
    public static function getExpiresAt()
    {
        return self::$expiresAt;
    }

}