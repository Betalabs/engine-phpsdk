<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;

class Header
{

    /** @var bool */
    protected $mustAuthorize = true;

    /** @var string */
    protected $bearerToken;

    /**
     * @param string $bearerToken
     */
    public function setBearerToken(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    /**
     * Retrieve Bearer token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     */
    public function retrieveToken()
    {

        if(is_null($this->bearerToken)) {
            throw new UnauthorizedException(
                'Token, e-mail and password not informed. Impossible to authenticate'
            );
        }

        return $this->bearerToken;

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