<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\GenesisClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\GenesisProviderNotDefinedException;

class GenesisProvider extends AbstractProvider
{

    /**
     * Build genesis provider based on configuration file
     *
     * @return \Betalabs\Engine\GenesisProvider
     * @throws \Betalabs\Engine\Configs\Exceptions\GenesisClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\GenesisProviderNotDefinedException
     */
    public function genesisProvider()
    {

        if(!isset($this->reader->load()->genesisProvider)) {
            throw new GenesisProviderNotDefinedException(
                'genesisProvider node does not exist in configuration file'
            );
        }

        $config = $this->reader->load()->genesisProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new GenesisClassDoesNotExistException(
                'Class '. $class .' defined in configuration does not exist'
            );
        }

        return $this->container->get($class);

    }

}