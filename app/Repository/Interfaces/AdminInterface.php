<?php

namespace App\Repository\Interfaces;

interface AdminInterface
{
    public function allLatestAdmin();
    public function allAdminList($relation, $column, $condition);
    public function getAnInstance($adminId);
    public function createAdmin(array $requestData);
    public function updateAdmin(array $requestData, $adminData);
    public function deleteAdmin($adminInfo);

    public function getAdminWithSpecificRole($rolesCondition, $whereCondition);
}
