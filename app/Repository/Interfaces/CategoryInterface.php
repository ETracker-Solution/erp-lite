<?php

namespace App\Repository\Interfaces;

interface CategoryInterface
{
    public function allLatestCategory();
    public function allCategoryList($relation, $column, $condition);
    public function getAnInstance($categoryId);
    public function createCategory(array $requestData);
    public function updateCategory(array $requestData, $categoryData);
    public function deleteCategory($categoryInfo);
}
