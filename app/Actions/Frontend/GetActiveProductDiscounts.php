<?php

namespace App\Actions\Frontend;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Collection;

class GetActiveProductDiscounts
{
    public function forProduct(Product $product): Product
    {
        return $this->execute(collect([$product]))->first();
    }

    public function execute(Collection $products, ?float $purchaseSubtotal = null): Collection
    {
        if ($products->isEmpty()) {
            return $products;
        }

        $productIds = $products->pluck('id')->unique()->values()->all();
        $categoryIds = $products->pluck('category_id')->filter()->unique()->values()->all();

        $discounts = Discount::query()
            ->with(['discountTargets' => function ($query) use ($productIds, $categoryIds) {
                $query->where(function ($query) use ($productIds, $categoryIds) {
                    $query->where(function ($query) use ($productIds) {
                        $query->where('target_type', 'PRODUCT')
                            ->whereIn('target_id', $productIds);
                    })->orWhere(function ($query) use ($categoryIds) {
                        $query->where('target_type', 'CATEGORY')
                            ->whereIn('target_id', $categoryIds);
                    });
                });
            }])
            ->where('level', 'PRODUCT')
            ->where('status', 'ACTIVE')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->where(function ($query) {
                $query->whereNull('usage_limit_total')
                    ->orWhereColumn('used_count', '<', 'usage_limit_total');
            })
            ->whereHas('discountTargets', function ($query) use ($productIds, $categoryIds) {
                $query->where(function ($query) use ($productIds, $categoryIds) {
                    $query->where(function ($query) use ($productIds) {
                        $query->where('target_type', 'PRODUCT')
                            ->whereIn('target_id', $productIds);
                    })->orWhere(function ($query) use ($categoryIds) {
                        $query->where('target_type', 'CATEGORY')
                            ->whereIn('target_id', $categoryIds);
                    });
                });
            })
            ->get();

        foreach ($products as $product) {
            $matchedDiscounts = $discounts->filter(
                fn (Discount $discount) => $this->matchesProduct($discount, $product)
                    && $this->meetsMinimumPurchase($discount, $purchaseSubtotal)
            );

            $bestDiscount = $matchedDiscounts->sortByDesc(
                fn (Discount $discount) => [
                    (int) $discount->priority,
                    $this->hasDirectTarget($discount, $product) ? 1 : 0,
                    -1 * ($discount->end_at?->getTimestamp() ?? 0),
                    (int) $discount->id,
                ]
            )->first();

            $price = (float) $product->price;
            $discountAmount = $bestDiscount ? $this->discountAmount($bestDiscount, $price) : 0.0;

            $product->setAttribute(
                'discount_badge',
                $bestDiscount ? $this->label($bestDiscount) : null
            );
            $product->setAttribute('discount_id', $bestDiscount?->id);
            $product->setAttribute('discount_amount', $discountAmount);
            $product->setAttribute('final_price', max(0.0, $price - $discountAmount));
        }

        return $products;
    }

    private function matchesProduct(Discount $discount, $product): bool
    {
        return $discount->discountTargets->contains(function ($target) use ($product) {
            if ($target->target_type === 'PRODUCT') {
                return (int) $target->target_id === (int) $product->id;
            }

            if ($target->target_type === 'CATEGORY') {
                return (int) $target->target_id === (int) $product->category_id;
            }

            return false;
        });
    }

    private function hasDirectTarget(Discount $discount, $product): bool
    {
        return $discount->discountTargets->contains(
            fn ($target) => $target->target_type === 'PRODUCT'
                && (int) $target->target_id === (int) $product->id
        );
    }

    private function meetsMinimumPurchase(Discount $discount, ?float $purchaseSubtotal): bool
    {
        if ($purchaseSubtotal === null) {
            return true;
        }

        return (float) $discount->min_purchase_amount <= $purchaseSubtotal;
    }

    private function discountAmount(Discount $discount, float $price): float
    {
        if ($price <= 0) {
            return 0.0;
        }

        if ($discount->value_type === 'PERCENTAGE') {
            $amount = $price * ((float) $discount->value / 100);

            if ($discount->max_discount_amount !== null) {
                $amount = min($amount, (float) $discount->max_discount_amount);
            }
        } else {
            $amount = (float) $discount->value;
        }

        return round(min(max($amount, 0.0), $price));
    }

    private function label(Discount $discount): string
    {
        if ($discount->value_type === 'PERCENTAGE') {
            $value = rtrim(rtrim(number_format((float) $discount->value, 2, '.', ''), '0'), '.');

            return $value.'% OFF';
        }

        return formatRupiah($discount->value).' OFF';
    }
}
