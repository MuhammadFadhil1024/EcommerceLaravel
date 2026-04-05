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

    public function searchCategory(string $search): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($search)) {
             return collect();
        }

        return Category::where('name', 'like', '%' . $search . '%')
            ->where('is_active', 1)
            ->take(5) // Batasi hasil agar dropdown tidak terlalu panjang
            ->get();
    }
}