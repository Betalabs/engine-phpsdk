<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;

class Token
{

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
                'Token not informed. Impossible to authenticate'
            );
        }

        return $this->bearerToken;

    }

}