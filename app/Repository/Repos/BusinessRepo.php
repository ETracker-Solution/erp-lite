<?php

namespace App\Repository\Repos;

use App\Models\Business;
use App\Repository\Interfaces\BusinessInterface;

class BusinessRepo implements BusinessInterface
{
    public function allLatestBusiness()
    {
        return Business::with('roles')->latest('id');
    }
    public function allBusinessList($relation, $column, $condition)
    {
        return Business::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($businessId)
    {
        return Business::findOrFail($businessId);
    }

    public function createBusiness($requestData)
    {
        return Business::create($requestData);
    }

    public function updateBusiness($requestData, $businessData)
    {
        return $businessData->update($requestData);
    }

    public function deleteBusiness($businessInfo)
    {
        return $businessInfo->delete();
    }
}
