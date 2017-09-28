<?php

namespace Betalabs\Engine\Permissions;

class Register
{

    /**
     * Add new permission
     *
     * @param \Betalabs\Engine\Permissions\Permission $permission
     * @return $this
     */
    public function add(Permission $permission)
    {
        Boot::addPermission($permission);
        return $this;
    }

}