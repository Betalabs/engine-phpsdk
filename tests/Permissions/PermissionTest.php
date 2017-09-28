<?php

namespace Betalabs\Engine\Tests\Permissions;

use Betalabs\Engine\Permissions\Permission;
use Betalabs\Engine\Tests\TestCase;

class PermissionTest extends TestCase
{

    public function testGettersAfterConstruct()
    {

        $permission = new Permission(
            'permission-name',
            'Display permission name',
            'Description permission description'
        );

        $this->assertEquals('permission-name', $permission->getName());
        $this->assertEquals('Display permission name', $permission->getDisplayName());
        $this->assertEquals('Description permission description', $permission->getDescription());


    }

}