<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Configs\Auth;
use Betalabs\Engine\Configs\Client;
use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;
use Betalabs\Engine\Database\Connection;
use Betalabs\Engine\Requests\Methods\Post;
use Carbon\Carbon;
use DI\Container;
use DI\ContainerBuilder;
use GuzzleHttp\Exception\ClientException;

class Token
{

    /** @var \Betalabs\Engine\Configs\Auth */
    protected $config;

    /** @var \DI\Container */
    protected $diContainer;

    /** @var */
    protected static $endpoint;

    /** @var string */
    protected static $accessToken;

    /** @var string */
    protected static $refreshToken;

    /** @var \Carbon\Carbon */
    protected static $expiresAt;

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
     * @param string $refreshToken
     * @param \Carbon\Carbon $expiresAt
     *
     * @return $this
     */
    public function informToken($accessToken, $refreshToken, $expiresAt)
    {
        self::$accessToken = $accessToken;
        self::$refreshToken = $refreshToken;
        self::$expiresAt = $expiresAt;

        $this->updateDatabaseTokens();
        return $this;
    }

    /**
     * Retrieve access token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    public function retrieveToken()
    {
        $this->retrieveConfig();

        if (is_null(self::$accessToken)) {
            if (!Credentials::isValid()) {
                throw new UnauthorizedException(
                    'Token not informed. Impossible to authenticate'
                );
            }

            $this->retrieveTokenByClientCredentials();
        }

        if (Carbon::now()->subSeconds(15) > self::$expiresAt) {
            $this->refreshToken();
        }

        return self::$accessToken;
    }

    /**
     * Refresh access token
     *
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     */
    public function refreshToken()
    {
        $this->retrieveConfig();

        if (is_null(self::$refreshToken)) {
            throw new TokenExpiredException('Token expired and there is no refresh token available');
        }

        $container = $this->diContainer();

        /** @var \Betalabs\Engine\Requests\Methods\Post $post */
        $post = $container->get(Post::class);

        /** @var \Betalabs\Engine\Configs\Client $client */
        $client = $container->get(Client::class);

        try {
            $post->mustNotAuthorize()->setEndpointSuffix(null);
            $response = $post->send(
                'oauth/token',
                [
                    'grant_type' => 'refresh_token',
                    'client_id' => $client->id(),
                    'client_secret' => $client->secret(),
                    'refresh_token' => self::$refreshToken,
                    'scope' => '*',
                ]
            );

            $this->informToken(
                $response->access_token,
                $response->refresh_token,
                Carbon::now()->addSeconds($response->expires_in)
            );
        } catch (ClientException $e) {
            if ($e->getCode() != 401 || !Credentials::isValid()) {
                throw $e;
            }

            $this->retrieveTokenByClientCredentials();
        }
    }

    /**
     * Retrieve data from configuration
     *
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    protected function retrieveConfig()
    {
        try {
            if (Credentials::isValid()) {
                $token = new \Betalabs\Engine\Database\Token();
                $tokenDB = $token->first();

                if ($tokenDB) {
                    self::$accessToken = $tokenDB->access_token;
                    self::$refreshToken = $tokenDB->refresh_token;
                    self::$expiresAt = Carbon::createFromTimestamp(
                        $tokenDB->expires_at
                    );
                    return;
                }
            }

            self::$accessToken = $this->config->accessToken();
            self::$refreshToken = $this->config->refreshToken();
            self::$expiresAt = Carbon::createFromTimestamp((string)$this->config->expiresAt());
        } catch (AuthNotDefinedException | AuthInternalNotDefinedException $e) {
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
        if (is_null($this->diContainer)) {
            $this->diContainer = ContainerBuilder::buildDevContainer();
        }

        return $this->diContainer;
    }

    /**
     * Update or insert a new token on table
     */
    public function updateDatabaseTokens()
    {
        $conn = Connection::get();

        try {
            $conn->beginTransaction();

            $token = new \Betalabs\Engine\Database\Token();
            $token->delete();
            $token->insert();

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }
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

    /**
     * Retrieve tokens by client credentials
     *
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     */
    private function retrieveTokenByClientCredentials()
    {
        $container = $this->diContainer();

        /** @var \Betalabs\Engine\Requests\Methods\Post $post */
        $post = $container->get(Post::class);

        /** @var \Betalabs\Engine\Configs\Client $client */
        $client = $container->get(Client::class);

        try {
            $post->mustNotAuthorize()->setEndpointSuffix(null);
            $response = $post->send('oauth/token', [
                'grant_type' => 'password',
                'username' => $client->username(),
                'password' => $client->password(),
                'client_id' => $client->id(),
                'client_secret' => $client->secret(),
                'scope' => '*',
            ]);

            $this->informToken(
                $response->access_token,
                $response->refresh_token,
                Carbon::now()->addSeconds($response->expires_in)
            );
        } catch (ClientException $e) {
            throw new UnauthorizedException(
                'Impossible to authenticate', $e->getCode(), $e
            );
        }
    }

}