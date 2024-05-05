<?php

namespace App\Repository\Interfaces;

interface PermissionInterface
{
    public function allPermissionList($relation, $column, $condition);
    public function createPermission($requestData);
    public function updatePermission($requestData, $permissionData);
    public function deletePermission($permissionInfo);
}
