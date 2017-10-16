<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\EnvironmentNotDefinedException;

class Environment extends AbstractProvider
{

    /**
     * Define which environment is being used
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException
     */
    public function environment()
    {

        if(!isset($this->environmentNode()->env)) {
            throw new EnvironmentInternalNodeNotDefinedException(
                'environment.env node does not exist in configuration file'
            );
        }

        return $this->environmentNode()->env;
    }

    /**
     * Define which URL must be used to access Engine API
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\EnvironmentInternalNodeNotDefinedException
     */
    public function endpoint()
    {

        if(!isset($this->environmentNode()->endpoint)) {
            throw new EnvironmentInternalNodeNotDefinedException(
                'environment.endpoint node does not exist in configuration file'
            );
        }

        return $this->environmentNode()->endpoint;

    }

    /**
     * Catch environment node
     *
     * @return \SimpleXMLElement[]
     * @throws \Betalabs\Engine\Configs\Exceptions\EnvironmentNotDefinedException
     */
    protected function environmentNode()
    {

        if(!isset($this->reader->load()->environment)) {
            throw new EnvironmentNotDefinedException(
                'environment node does not exist in configuration file'
            );
        }

        return $this->reader->load()->environment;

    }

}