<?php

namespace App\Repository\Repos;

use App\Models\Product;
use App\Repository\Interfaces\ProductInterface;

class ProductRepo implements ProductInterface
{
    public function allLatestProduct()
    {
        return Product::latest('id');
    }
    public function allProductList($relation, $column, $condition)
    {
        return Product::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($productId)
    {
        return Product::findOrFail($productId);
    }

    public function createProduct($requestData)
    {
        return Product::create($requestData);
    }

    public function updateProduct($requestData, $productData)
    {
        return $productData->update($requestData);
    }

    public function deleteProduct($productInfo)
    {
        return $productInfo->delete();
    }
}
