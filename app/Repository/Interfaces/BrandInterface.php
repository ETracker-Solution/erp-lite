<?php

namespace App\Repository\Interfaces;

interface BrandInterface
{
    public function allLatestBrand();
    public function allBrandList($relation, $column, $condition);
    public function getAnInstance($brandId);
    public function createBrand(array $requestData);
    public function updateBrand(array $requestData, $brandData);
    public function deleteBrand($brandInfo);
}
