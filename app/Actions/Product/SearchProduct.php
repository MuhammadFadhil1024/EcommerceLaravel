<?php

namespace App\Actions\Product;

use App\Models\Product;

class SearchProduct
{
    public function execute(string $search = '', int $limit = 20)
    {
        return Product::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function findByIds(array $ids)
    {
        if (empty($ids)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }
}
