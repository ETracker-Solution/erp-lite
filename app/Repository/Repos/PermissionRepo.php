<?php

namespace App\Repository\Repos;

use App\Repository\Interfaces\PermissionInterface;
use Spatie\Permission\Models\Permission;

class PermissionRepo implements PermissionInterface
{

    public function allPermissionList($relation, $column, $condition)
    {
        return Permission::with($relation)->select($column)->where($condition)->latest();
    }


    public function createPermission($requestData)
    {
        return Permission::create($requestData);
    }

    public function updatePermission($requestData, $permissionData)
    {
        // TODO: Implement updatePermission() method.
    }

    public function deletePermission($permissionInfo)
    {
        // TODO: Implement deletePermission() method.
    }
}
