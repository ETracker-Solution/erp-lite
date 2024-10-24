<?php

namespace App\Repository\Repos;

use App\Models\Category;
use App\Repository\Interfaces\CategoryInterface;

class CategoryRepo implements CategoryInterface
{
    public function allLatestCategory()
    {
        return Category::latest('id');
    }
    public function allCategoryList($relation, $column, $condition)
    {
        return Category::with($relation)->select($column)->where($condition)->get();
    }
    public function getAnInstance($categoryId)
    {
        return Category::findOrFail($categoryId);
    }

    public function createCategory($requestData)
    {
        return Category::create($requestData);
    }

    public function updateCategory($requestData, $categoryData)
    {
        return $categoryData->update($requestData);
    }

    public function deleteCategory($categoryInfo)
    {
        return $categoryInfo->delete();
    }
}
