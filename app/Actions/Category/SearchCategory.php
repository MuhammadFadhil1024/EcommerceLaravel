<?php

namespace App\Actions\Category;

use App\Models\Category;

class SearchCategory
{
    public function execute(string $search = '', int $limit = 20)
    {
        return Category::query()
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

        return Category::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }
}
