<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\DatabaseClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\DatabaseProviderNotDefinedException;

class DatabaseProvider extends AbstractProvider
{

    /**
     * Build database provider based on configuration file
     *
     * @return \Betalabs\Engine\DatabaseProvider
     * @throws \Betalabs\Engine\Configs\Exceptions\DatabaseClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\DatabaseProviderNotDefinedException
     */
    public function databaseProvider()
    {

        if(!isset($this->reader->load()->databaseProvider)) {
            throw new DatabaseProviderNotDefinedException(
                'databaseProvider node does not exist in configuration file'
            );
        }

        $config = $this->reader->load()->databaseProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new DatabaseClassDoesNotExistException(
                'Class '. $class .' defined in configuration does not exist'
            );
        }

        return $this->container->get($class);

    }

}