<?php

namespace App\Traits;

trait HasPermissions
{
    protected $permissionList = null;

    public function userCan($permission = null)
    {
        return $this->hasPermission($permission);
    }
    public function hasPermission($permission = null)
    {
        $user_roles = $this->group->roles->pluck('name')->toArray();
        return in_array($permission,$user_roles);
    }
}
