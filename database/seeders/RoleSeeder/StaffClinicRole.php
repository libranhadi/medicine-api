<?php

namespace Database\Seeders\RoleSeeder;

use App\PermissionManager\StaffClinicPermissionManager;
use Database\Seeders\RoleSeeder\Core\RoleAndPermissionSeedRunner;

class StaffClinicRole extends RoleAndPermissionSeedRunner
{
    protected $roleName = "Staff";
    protected $permissionManager = StaffClinicPermissionManager::class;
}
