<?php

function checkACL($user, $permissionName) {
    if (!$user->hasPermissionTo($permissionName)) {
       return false;
    }
    return true;
}