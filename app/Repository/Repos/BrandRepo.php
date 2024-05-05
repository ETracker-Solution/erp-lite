<?php

namespace App\Repository\Repos;

use App\Models\Brand;
use App\Repository\Interfaces\BrandInterface;

class BrandRepo implements BrandInterface
{
    public function allLatestBrand()
    {
        return Brand::latest('id');
    }
    public function allBrandList($relation, $column, $condition)
    {
        return Brand::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($brandId)
    {
        return Brand::findOrFail($brandId);
    }

    public function createBrand($requestData)
    {
        return Brand::create($requestData);
    }

    public function updateBrand($requestData, $brandData)
    {
        return $brandData->update($requestData);
    }

    public function deleteBrand($brandInfo)
    {
        return $brandInfo->delete();
    }
}
