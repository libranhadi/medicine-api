<?php 

namespace App\PermissionManager;

class StaffClinicPermissionManager extends PermissionManager 
{
    public $permissions = [
        // Permission for medicine outgoing feature
        'Can - Read Medicine Outgoing',
        'Can - Create Medicine Outgoing',
        // 'Can - Update Medicine Outgoing',
        // 'Can - Delete Medicine Outgoing',
    ];
}