<?php


namespace Betalabs\Engine\Configs;


use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;

class Cache extends AbstractProvider
{

    /**
     * Return driver
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function driver()
    {
        if (!isset($this->cacheNode()->driver)) {
            throw new AuthInternalNotDefinedException(
                'driver does not exist in configuration file'
            );
        }

        return $this->cacheNode()->driver;
    }

    /**
     * Return host
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function host()
    {
        if (!isset($this->cacheNode()->host)) {
            throw new AuthInternalNotDefinedException(
                'host does not exist in configuration file'
            );
        }

        return $this->cacheNode()->host;
    }

    /**
     * Return port
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function port()
    {
        if (!isset($this->cacheNode()->port)) {
            throw new AuthInternalNotDefinedException(
                'port does not exist in configuration file'
            );
        }

        return $this->cacheNode()->port;
    }

    /**
     * Return password
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function password()
    {
        if (!isset($this->cacheNode()->password)) {
            throw new AuthInternalNotDefinedException(
                'password does not exist in configuration file'
            );
        }

        return $this->cacheNode()->password;
    }

    /**
     * Catch cache node
     *
     * @return \SimpleXMLElement
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    protected function cacheNode()
    {
        if (!isset($this->reader->load()->cache)) {
            throw new AuthNotDefinedException(
                'cache node does not exist in configuration file'
            );
        }

        return $this->reader->load()->cache;
    }
}