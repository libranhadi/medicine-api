<?php

namespace Database\Seeders\RoleSeeder\Core;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeedRunner
{
    protected $roleName = '';
    protected $permissionManager = '';

    public function seed()
    {
        $role = Role::where('name', $this->roleName)->first();
        
        if (empty($role)) {
            $role = Role::create(['name'=> $this->roleName]);
        }

        $permissionsManager = new $this->permissionManager;
        $permissions = $permissionsManager->getPermissions();

        // Revoke existing permissions not in the new list
        $existingPermissions = $role->permissions()->pluck('name')->toArray();
        $revoke = array_diff($existingPermissions, $permissions);

        if (count($revoke) > 0) {
            $role->revokePermissionTo($revoke);
            echo $this->roleName." REVOKING PERMISSIONS: " .implode(", ", $revoke)."\n";
        }

        // Grant new permissions
        foreach ($permissions as $permissionName) {
            echo $this->roleName ." Granting Permission: ". $permissionName . "\n";
            $permission = Permission::where('name', $permissionName)->first();
            if (!$permission) {
                $permission = Permission::create(['name' => $permissionName]);
            }

            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}