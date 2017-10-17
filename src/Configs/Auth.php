<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;

class Auth extends AbstractProvider
{

    /**
     * Return accessToken
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     */
    public function accessToken()
    {

        if(!isset($this->environmentNode()->accessToken)) {
            throw new AuthInternalNotDefinedException(
                'accessToken does not exist in configuration file'
            );
        }

        return $this->environmentNode()->accessToken;

    }

    /**
     * Return refreshToken
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     */
    public function refreshToken()
    {

        if(!isset($this->environmentNode()->refreshToken)) {
            throw new AuthInternalNotDefinedException(
                'refreshToken does not exist in configuration file'
            );
        }

        return $this->environmentNode()->refreshToken;

    }

    /**
     * Return expiresAt for accessToken
     *
     * @return int
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     */
    public function expiresAt()
    {

        if(!isset($this->environmentNode()->expiresAt)) {
            throw new AuthInternalNotDefinedException(
                'expiresAt does not exist in configuration file'
            );
        }

        return $this->environmentNode()->expiresAt;

    }

    /**
     * Catch auth node
     *
     * @return \SimpleXMLElement[]
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     */
    protected function environmentNode()
    {

        if(!isset($this->reader->load()->auth)) {
            throw new AuthNotDefinedException(
                'auth node does not exist in configuration file'
            );
        }

        return $this->reader->load()->auth;

    }

}