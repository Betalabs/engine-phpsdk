<?php

namespace Betalabs\Engine;

use Betalabs\Engine\Auth\Token;
use Carbon\Carbon;

class Listener
{

    public function listen()
    {

        $this->engineHeaders();

    }

    /**
     * Search for Engine headers to assign token
     */
    protected function engineHeaders()
    {

        $token = $this->getHeader('HTTP_ENGINE_TOKEN');
        $expiresAt = $this->getHeader('HTTP_ENGINE_TOKEN_EXPIRES_AT');

        if(is_null($token) || is_null($expiresAt)) {
            return;
        }

        $token = new Token();
        $token->informBearerToken($token, Carbon::createFromTimestamp($expiresAt));

    }

    /**
     * Get the header from server or returns null
     *
     * @param $variable
     * @return null|string
     */
    protected function getHeader($variable)
    {

        if(!isset($_SERVER[$variable])) {
            return null;
        }

        return $_SERVER[$variable];

    }

}