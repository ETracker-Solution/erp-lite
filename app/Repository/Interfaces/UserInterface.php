<?php

namespace App\Repository\Interfaces;

interface UserInterface
{
    public function allLatestUser();
    public function allUserList($relation, $column, $condition);
    public function getAnInstance($userId);
    public function createUser(array $requestData);
    public function updateUser(array $requestData, $userData);
    public function deleteUser($userInfo);

    public function getUserWithSpecificRole($rolesCondition, $whereCondition);
}
