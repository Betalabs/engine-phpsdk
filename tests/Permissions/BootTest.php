<?php

namespace Betalabs\Engine\Tests\Permissions;

use Betalabs\Engine\Configs\Exceptions\PermissionProviderNotDefinedException;
use Betalabs\Engine\Configs\PermissionProvider as PermissionProviderConfig;
use Betalabs\Engine\PermissionProvider;
use Betalabs\Engine\Permissions\Boot;
use Betalabs\Engine\Permissions\Permission;
use Betalabs\Engine\Permissions\Register;
use Betalabs\Engine\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BootTest extends TestCase
{

    /**
     * @after
     * @before
     */
    public function clearPermissions()
    {
        Boot::clearPermissions();
    }

    public function testExistingPermissionsToRender()
    {

        $register = \Mockery::mock(Register::class);

        $permissionProvider = \Mockery::mock(PermissionProvider::class);
        $permissionProvider->shouldReceive('permissions')
            ->once()
            ->with($register);

        $permissionProviderConfig = \Mockery::mock(PermissionProviderConfig::class);
        $permissionProviderConfig->shouldReceive('permissionProvider')
            ->once()
            ->andReturn($permissionProvider);

        Boot::addPermission(new Permission(
            'permission-0-name',
            'Display permission #0',
            'Description permission #0'
        ));

        Boot::addPermission(new Permission(
            'permission-1-name',
            'Display permission #1',
            'Description permission #1'
        ));

        $boot = new Boot($permissionProviderConfig, $register);

        $this->assertEquals(
            [
                [
                    'name' => 'permission-0-name',
                    'display_name' => 'Display permission #0',
                    'description' => 'Description permission #0'
                ], [
                    'name' => 'permission-1-name',
                    'display_name' => 'Display permission #1',
                    'description' => 'Description permission #1'
                  ]
            ],
            $boot->render()
        );

    }

    public function testEmptyPermissionsThrowsException()
    {

        $this->expectException(NotFoundHttpException::class);

        $register = \Mockery::mock(Register::class);

        $permissionProvider = \Mockery::mock(PermissionProvider::class);
        $permissionProvider->shouldReceive('permissions')
            ->once()
            ->with($register);

        $permissionProviderConfig = \Mockery::mock(PermissionProviderConfig::class);
        $permissionProviderConfig->shouldReceive('permissionProvider')
            ->once()
            ->andReturn($permissionProvider);

        $boot = new Boot($permissionProviderConfig, $register);

        $boot->render();

    }

    public function testNonExistingPermissionProviderThrowsException()
    {

        $this->expectException(NotFoundHttpException::class);

        $permissionProviderConfig = \Mockery::mock(PermissionProviderConfig::class);
        $permissionProviderConfig->shouldReceive('permissionProvider')
            ->once()
            ->andThrow(PermissionProviderNotDefinedException::class);

        $register = \Mockery::mock(Register::class);

        $boot = new Boot($permissionProviderConfig, $register);

        $boot->render();

    }

}