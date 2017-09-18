<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Request\Methods\Post;

class Token
{

    /** @var \Betalabs\Engine\Request\Methods\Post */
    protected $postRequest;

    /** @var int */
    protected $clientId;

    /** @var string */
    protected $clientSecret;

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Token constructor.
     * @param \Betalabs\Engine\Request\Methods\Post $postRequest
     */
    public function __construct(Post $postRequest)
    {
        $this->postRequest = $postRequest;
    }

    /**
     * Request access token for given email and password
     *
     * @param string $email
     * @param string $password
     * @return string
     */
    public function request($email, $password)
    {

        $this->postRequest
            ->mustNotAuthorize()
            ->send('oauth/token', [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
        ], false);

        return $this->accessToken();

    }

    /**
     * Retrieve access token from response
     *
     * @return string
     */
    public function accessToken()
    {
        return $this->postRequest->getContents()->access_token;
    }

    /**
     * Retrieve refresh token from response
     *
     * @return string
     */
    public function refreshToken()
    {
        return $this->postRequest->getContents()->refresh_token;
    }

}