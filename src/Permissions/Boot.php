<?php

namespace Betalabs\Engine\Permissions;

use Betalabs\Engine\Configs\Exceptions\PermissionProviderNotDefinedException;
use Betalabs\Engine\Configs\PermissionProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Boot
{

    /** @var \Betalabs\Engine\Permissions\Permission[] */
    protected static $permissions = [];

    /** @var \Betalabs\Engine\Configs\PermissionProvider */
    protected $permissionProvider;

    /** @var \Betalabs\Engine\Permissions\Register */
    protected $register;

    /**
     * Boot constructor.
     *
     * @param \Betalabs\Engine\Configs\PermissionProvider $permissionProvider
     * @param \Betalabs\Engine\Permissions\Register $register
     */
    public function __construct(
        PermissionProvider $permissionProvider,
        Register $register
    ) {

        $this->permissionProvider = $permissionProvider;
        $this->register = $register;
    }

    /**
     * Add new permission
     *
     * @param \Betalabs\Engine\Permissions\Permission $permission
     */
    public static function addPermission(Permission $permission)
    {
        self::$permissions[] = $permission;
    }

    /**
     * Render permission JSON
     *
     * @return array
     */
    public function render()
    {

        $this->providePermission();

        if(empty(self::$permissions)) {
            $this->permissionsNotDefined();
        }

        return $this->renderArray();

    }

    /**
     * Run PermissionProvider to add defined package permissions
     */
    protected function providePermission()
    {

        try {

            $permissionProvider = $this->permissionProvider->permissionProvider();
            $permissionProvider->permissions($this->register);

        } catch(PermissionProviderNotDefinedException $e) {
            $this->permissionsNotDefined();
        }

    }

    /**
     * Make permission array to be render
     *
     * @return array
     */
    protected function renderArray(): array
    {
        return array_map(function (Permission $permission) {

            return [
                'name' => $permission->getName(),
                'display_name' => $permission->getDisplayName(),
                'description' => $permission->getDescription()
            ];

        }, self::$permissions);

    }

    /**
     * @return \Betalabs\Engine\Permissions\Permission[]
     */
    public static function getPermissions(): array
    {
        return self::$permissions;
    }

    /**
     * @param \Betalabs\Engine\Permissions\Permission[] $permissions
     */
    public static function setPermissions(array $permissions)
    {
        self::$permissions = $permissions;
    }

    /**
     * Inform Engine permissions were not defined
     */
    protected function permissionsNotDefined()
    {
        throw new NotFoundHttpException();
    }


}