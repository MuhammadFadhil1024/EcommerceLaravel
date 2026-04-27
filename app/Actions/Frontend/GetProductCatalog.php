<?php

namespace App\Actions\Frontend;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class GetProductCatalog
{
    public function getCategories()
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getProducts(string $search = '', ?int $categoryId = null, int $limit = 20)
    {
        return $this->buildQuery($search, $categoryId)
            ->with(['galleries', 'category'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function countProducts(string $search = '', ?int $categoryId = null): int
    {
        return $this->buildQuery($search, $categoryId)->count();
    }

    protected function buildQuery(string $search, ?int $categoryId): Builder
    {
        return Product::query()
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($categoryId, function (Builder $query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
    }
}
