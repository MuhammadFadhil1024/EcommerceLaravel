<?php

namespace App\Actions\Discount;

use App\Models\Discount;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchDiscounts
{
    public function handle(
        string $search = '',
        string $type = '',
        string $level = '',
        int $perPage = 10,
    ): LengthAwarePaginator
    {
        // Filter langsung di PHP, tanpa closure di query builder
        $query = Discount::query()->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($type !== '') {
            $query->where('value_type', $type);
        }

        if ($level !== '') {
            $query->where('level', $level);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}