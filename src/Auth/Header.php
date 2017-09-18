<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Request\Header as RequestHeader;
use Betalabs\Engine\Request\Methods\Post;
use DI\ContainerBuilder;
use GuzzleHttp\Client;

class Header
{

    /** @var \Betalabs\Engine\Auth\Token */
    protected $token;

    /** @var bool */
    protected $mustAuthorize = true;

    /** @var string */
    protected $bearerToken;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /**
     * @param string $bearerToken
     */
    public function setBearerToken(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param \Betalabs\Engine\Auth\Token $token
     */
    public function setToken(\Betalabs\Engine\Auth\Token $token)
    {
        $this->token = $token;
    }

    /**
     * Retrieve Bearer token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     */
    public function retrieveToken()
    {

        if(!is_null($this->bearerToken)) {
            return $this->bearerToken;
        }

        if(is_null($this->email) || is_null($this->password)) {
            throw new UnauthorizedException(
                'Token, e-mail and password not informed. Impossible to authenticate'
            );
        }

        return $this->bearerToken = $this->token()->request(
            $this->email,
            $this->password
        );

    }

    protected function token()
    {

        if(!is_null($this->token)) {
            return $this->token;
        }

        $container = ContainerBuilder::buildDevContainer();
        return $this->token = $container->get(Token::class);

    }

    /**
     * Build header array with authenticate if required
     *
     * @return array
     */
    public function header()
    {

        if(!$this->mustAuthorize) {
            return [];
        }

        return [
            'Authorization' => 'Bearer '. $this->retrieveToken()
        ];

    }

    /**
     * Define authorization is required
     *
     * @return $this
     */
    public function mustAuthorize()
    {
        $this->mustAuthorize = true;
        return $this;
    }

    /**
     * Define authorization is not required
     *
     * @return $this
     */
    public function mustNotAuthorize()
    {
        $this->mustAuthorize = false;
        return $this;
    }

}