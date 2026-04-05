<?php 

namespace App\Actions\Category;

use App\Models\Category;

Class GetCategory
{

    /**
     * Mengeksekusi pengambilan semua kategori.
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getALlCategory()
    {
        $categories = Category::query()->paginate(10);

        return $categories;
    }


    public function getCategoryById(int $categoryId): ?Category
    {
        return Category::findOrFail($categoryId);
    }

    public function checkAvailableProduct(int $categoryId): int
    {
        $category = Category::findOrFail($categoryId);
        if ($category->products()->exists()) {
            return true;
        } else {
            return false;
        }
    }
}