<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\MigrationClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\MigrationProviderNotDefinedException;

class MigrationProvider extends AbstractProvider
{

    /**
     * Build database provider based on configuration file
     *
     * @return \Betalabs\Engine\MigrationProvider
     * @throws \Betalabs\Engine\Configs\Exceptions\MigrationClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\MigrationProviderNotDefinedException
     */
    public function migrationProvider()
    {

        if(!isset($this->reader->load()->migrationProvider)) {
            throw new MigrationProviderNotDefinedException(
                'migrationProvider node does not exist in configuration file'
            );
        }

        $config = $this->reader->load()->migrationProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new MigrationClassDoesNotExistException(
                'Class '. $class .' defined in configuration does not exist'
            );
        }

        return $this->container->get($class);

    }

}