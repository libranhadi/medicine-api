<?php 

namespace App\PermissionManager;

class PermissionManager 
{
    /**
     * The Permission that this user level can access
     * 
     * @var array
     */
    public $permissions = array();

    /**
     * return to the permissions
     */
    public function getPermissions() : array 
    {
        return $this->permissions;
    }
}