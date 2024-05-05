<?php

namespace App\Repository\Interfaces;

interface BusinessInterface
{
    public function allLatestBusiness();
    public function allBusinessList($relation, $column, $condition);
    public function getAnInstance($businessId);
    public function createBusiness(array $requestData);
    public function updateBusiness(array $requestData, $businessData);
    public function deleteBusiness($businessInfo);
}
