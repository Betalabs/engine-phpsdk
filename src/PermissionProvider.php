<?php

namespace Betalabs\Engine;

interface PermissionProvider
{

    /**
     * Add all used permissions
     *
     * @param \Betalabs\Engine\Permissions\Register $register
     * @return void
     */
    public function permissions(\Betalabs\Engine\Permissions\Register $register);

}