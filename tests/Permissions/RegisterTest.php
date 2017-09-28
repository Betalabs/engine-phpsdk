<?php

namespace Betalabs\Engine\Tests\Permissions;

use Betalabs\Engine\Permissions\Boot;
use Betalabs\Engine\Permissions\Permission;
use Betalabs\Engine\Permissions\Register;
use Betalabs\Engine\Tests\TestCase;

class RegisterTest extends TestCase
{

    /**
     * @after
     * @before
     */
    public function clearPermissions()
    {
        Boot::clearPermissions();
    }

    public function testAddPermission()
    {

        $register = new Register();

        $permissions = [];

        $permissions[0] = new Permission(
            'permission-0-name',
            'Display permission #0',
            'Description permission #0'
        );

        $permissions[1] = new Permission(
            'permission-1-name',
            'Display permission #1',
            'Description permission #1'
        );

        $register->add($permissions[0]);
        $register->add($permissions[1]);

        $this->assertEquals($permissions, Boot::getPermissions());

    }

}