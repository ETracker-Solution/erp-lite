<?php

namespace App\Repository\Repos;

use App\Repository\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;

class RoleRepo implements RoleInterface
{

    public function allRoleList($relation, $column, $condition)
    {
        return Role::with($relation)->select($column)->where($condition)->latest();
    }
}
