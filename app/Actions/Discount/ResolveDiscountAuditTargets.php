<?php

namespace App\Actions\Discount;

use App\Models\Category;
use App\Models\Product;

class ResolveDiscountAuditTargets
{
    public function resolve(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $targetIds = $data['targets'] ?? [];

        if (empty($targetIds)) {
            $data['targets'] = [];

            return $data;
        }

        $names = $this->names($data['target_type'] ?? null, $targetIds);

        $data['targets'] = array_map(
            fn ($id) => $names[$id] ?? '#'.$id,
            $targetIds
        );

        return $data;
    }

    private function names(?string $type, array $ids): array
    {
        return match ($type) {
            'PRODUCT' => Product::whereIn('id', $ids)->pluck('name', 'id')->all(),
            'CATEGORY' => Category::whereIn('id', $ids)->pluck('name', 'id')->all(),
            default => [],
        };
    }
}
