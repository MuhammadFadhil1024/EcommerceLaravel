<?php

namespace App\Actions\Discount;

use App\Models\Discount;
// use Illuminate\Pagination\LengthAwarePaginator;

class SearchDiscounts
{
    public function execute(array $filters, int $perPage = 10)
    {
        $query = Discount::query()->orderByDesc('created_at');

        // Filter search: carike name atau code
        if ($filters['search'] !== '') {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('code', 'LIKE', "%{$filters['search']}%");
            });
        }

        // Filter type: Fixed / Percentage
        if ($filters['type'] !== '') {
            $query->where('value_type', $filters['type']);
        }

        // Filter level: PRODUCT / TRANSACTION
        if ($filters['level'] !== '') {
            $query->where('level', $filters['level']);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}