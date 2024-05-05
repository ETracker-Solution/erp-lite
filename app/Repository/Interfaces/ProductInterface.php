<?php

namespace App\Repository\Interfaces;

interface ProductInterface
{
    public function allLatestProduct();
    public function allProductList($relation, $column, $condition);
    public function getAnInstance($productId);
    public function createProduct(array $requestData);
    public function updateProduct(array $requestData, $productData);
    public function deleteProduct($productInfo);
}
