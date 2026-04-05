<?php 

namespace App\Actions\Category;

use App\Models\Category;

Class DeleteCategory
{
    public function delete(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->delete();
    }
}