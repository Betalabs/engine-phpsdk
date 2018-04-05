<?php


namespace Betalabs\Engine\Configs;


class Cache extends AbstractProvider
{

    /**
     * Return driver
     *
     * @return string|null
     */
    public function driver()
    {
        if (!isset($this->cacheNode()->driver)) {
            return null;
        }

        return $this->cacheNode()->driver;
    }

    /**
     * Return host
     *
     * @return string|null
     */
    public function host()
    {
        if (!isset($this->cacheNode()->host)) {
            return null;
        }

        return $this->cacheNode()->host;
    }

    /**
     * Return port
     *
     * @return string|null
     */
    public function port()
    {
        if (!isset($this->cacheNode()->port)) {
            return null;
        }

        return $this->cacheNode()->port;
    }

    /**
     * Return password
     *
     * @return string|null
     */
    public function password()
    {
        if (!isset($this->cacheNode()->password)) {
            return null;
        }

        return $this->cacheNode()->password;
    }

    /**
     * Catch cache node
     *
     * @return \SimpleXMLElement|null
     */
    protected function cacheNode()
    {
        if (!isset($this->reader->load()->cache)) {
            return null;
        }

        return $this->reader->load()->cache;
    }
}