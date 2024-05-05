<?php

namespace App\Repository\Repos;

use App\Models\User;
use App\Repository\Interfaces\UserInterface;

class UserRepo implements UserInterface
{
    public function allLatestUser()
    {
        return User::with('roles')->latest('id');
    }
    public function allUserList($relation, $column, $condition)
    {
        return User::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($userId)
    {
        return User::findOrFail($userId);
    }

    public function createUser($requestData)
    {
        return User::create($requestData);
    }

    public function updateUser($requestData, $userData)
    {
        return $userData->update($requestData);
    }

    public function deleteUser($userInfo)
    {
        return $userInfo->delete();
    }

    public function getUserWithSpecificRole($rolesCondition, $whereCondition = [])
    {
        return User::with('roles')->whereHas('roles', function ($q) use ($rolesCondition) {
            $q->where($rolesCondition);
        })->where($whereCondition)->get();
    }
}
