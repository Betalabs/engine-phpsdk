<?php

namespace Betalabs\Engine\Requests;

use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Configs\Environment;
use Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException;

class EndpointResolver
{

    /** @var string */
    private static $endpoint;

    /** @var \Betalabs\Engine\Configs\Environment */
    protected $conf;

    /**
     * EndpointResolver constructor.
     * @param \Betalabs\Engine\Configs\Environment $conf
     */
    public function __construct(Environment $conf)
    {
        $this->conf = $conf;
    }

    /**
     * Define endpoint for requests
     *
     * @return string
     */
    public function endpoint()
    {
        try {
            // If credentials is setted it will be used
            if (Credentials::isValid()) {
                return Credentials::$apiUri;
            }

            // If an endpoint is defined it will be used
            return $this->conf->endpoint();
        } catch(EnvironmentInternalNodeNotDefinedException $e) {
            return $this->environmentEndpoint();
        }
    }

    /**
     * Define endpoint based on environment
     *
     * @return string
     */
    protected function environmentEndpoint()
    {
        try {

            // If environment is Sandbox
            if($this->conf->environment() == 'Sandbox') {
                return 'https://api.sandbox.betalabs.net';
            }

            return $this->defaultEndpoint();

        } catch(EnvironmentInternalNodeNotDefinedException $e) {
            return $this->defaultEndpoint();
        }
    }

    /**
     * Default endpoint
     *
     * @return string
     */
    protected function defaultEndpoint()
    {
        if(self::$endpoint) {
            return self::$endpoint;
        }

        return 'http://engine.local';
    }

    /**
     * @param string $endpoint
     */
    public static function setEndpoint($endpoint)
    {
        self::$endpoint = $endpoint;
    }

}