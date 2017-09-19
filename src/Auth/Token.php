<?php

namespace Betalabs\Engine\Auth;

use Betalabs\Engine\Auth\Exceptions\TokenExpiredException;
use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Carbon\Carbon;

class Token
{

    /** @var string */
    protected static $bearerToken;

    /** @var \Carbon\Carbon */
    protected static $expiresAt;

    /**
     * Inform the bearer token information
     *
     * @param string $bearerToken
     * @param \Carbon\Carbon $expiresAt
     * @return $this
     */
    public function informBearerToken($bearerToken, $expiresAt)
    {
        self::$bearerToken = $bearerToken;
        self::$expiresAt = $expiresAt;
        return $this;
    }

    /**
     * Retrieve Bearer token
     *
     * @return string
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     */
    public function retrieveToken()
    {

        if(is_null(self::$bearerToken)) {
            throw new UnauthorizedException(
                'Token not informed. Impossible to authenticate'
            );
        }

        if(Carbon::now() > self::$expiresAt) {
            throw new TokenExpiredException();
        }

        return self::$bearerToken;

    }

}