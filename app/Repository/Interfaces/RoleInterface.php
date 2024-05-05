<?php

namespace App\Repository\Interfaces;

interface RoleInterface
{
    public function allRoleList($relation, $column, $condition);
}
