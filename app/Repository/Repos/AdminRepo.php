<?php

namespace App\Repository\Repos;

use App\Models\Admin;
use App\Repository\Interfaces\AdminInterface;

class AdminRepo implements AdminInterface
{
    public function allLatestAdmin()
    {
        return Admin::with('roles')->latest('id');
    }
    public function allAdminList($relation, $column, $condition)
    {
        return Admin::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($adminId)
    {
        return Admin::findOrFail($adminId);
    }

    public function createAdmin($requestData)
    {
        return Admin::create($requestData);
    }

    public function updateAdmin($requestData, $adminData)
    {
        return $adminData->update($requestData);
    }

    public function deleteAdmin($adminInfo)
    {
        return $adminInfo->delete();
    }

    public function getAdminWithSpecificRole($rolesCondition, $whereCondition = [])
    {
        return Admin::with('roles')->whereHas('roles', function ($q) use ($rolesCondition) {
            $q->where($rolesCondition);
        })->where($whereCondition)->get();
    }
}
