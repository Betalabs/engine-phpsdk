<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\PermissionClassDoesNotExistException;
use Betalabs\Engine\Configs\Exceptions\PermissionProviderNotDefinedException;

class PermissionProvider extends AbstractProvider
{

    /**
     * Build permission provider based on configuration file
     *
     * @return \Betalabs\Engine\PermissionProvider
     * @throws \Betalabs\Engine\Configs\Exceptions\PermissionClassDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PermissionProviderNotDefinedException
     */
    public function permissionProvider()
    {

        if(!isset($this->reader->load()->permissionProvider)) {
            throw new PermissionProviderNotDefinedException(
                'permissionProvider node does not exist in configuration file'
            );
        }

        $config = $this->reader->load()->permissionProvider;

        if(isset($config->path) && !empty($config->path)) {
            $this->requireClassPath($config);
        }

        $class = (string) $config->class;

        if(!$this->helper->classExists($class)) {
            throw new PermissionClassDoesNotExistException(
                'Class '. $class .' defined in configuration does not exist'
            );
        }

        return $this->container->get($class);

    }

}