<?php

use Betalabs\Engine\PermissionProvider;

class Permission implements PermissionProvider
{

    /**
     * Add all used permissions
     *
     * @param \Betalabs\Engine\Permissions\Register $register
     * @return void
     */
    public function permissions(\Betalabs\Engine\Permissions\Register $register)
    {

        $register->add(new \Betalabs\Engine\Permissions\Permission(
            'permission-0-name',
            'Permission #0 name',
            'Permission #0 description'
        ));

        $register->add(new \Betalabs\Engine\Permissions\Permission(
            'permission-1-name',
            'Permission #1 name',
            'Permission #1 description'
        ));

    }
}