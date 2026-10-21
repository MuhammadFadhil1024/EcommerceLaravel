<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->value('id') ?? User::factory()->create([
            'name' => 'Admin Seeder',
            'email' => 'admin-seeder@example.com',
        ])->id;

        $promos = [
            'oslo-3-seater-sofa' => ['value_type' => 'PERCENTAGE', 'value' => 10, 'max_discount_amount' => 500000, 'status' => 'ACTIVE'],
            'luna-coffee-table' => ['value_type' => 'FIXED_AMOUNT', 'value' => 250000, 'max_discount_amount' => null, 'status' => 'DRAFT'],
            'aurora-queen-bed' => ['value_type' => 'PERCENTAGE', 'value' => 10, 'max_discount_amount' => 500000, 'status' => 'INACTIVE'],
            'haven-wardrobe' => ['value_type' => 'FIXED_AMOUNT', 'value' => 250000, 'max_discount_amount' => null, 'status' => 'ACTIVE'],
            'oak-dining-table' => ['value_type' => 'PERCENTAGE', 'value' => 10, 'max_discount_amount' => 500000, 'status' => 'DRAFT'],
            'velvet-dining-chair' => ['value_type' => 'FIXED_AMOUNT', 'value' => 250000, 'max_discount_amount' => null, 'status' => 'INACTIVE'],
            'ergo-office-chair' => ['value_type' => 'PERCENTAGE', 'value' => 10, 'max_discount_amount' => 500000, 'status' => 'ACTIVE'],
            'walnut-writing-desk' => ['value_type' => 'FIXED_AMOUNT', 'value' => 250000, 'max_discount_amount' => null, 'status' => 'DRAFT'],
            'teak-patio-set' => ['value_type' => 'PERCENTAGE', 'value' => 10, 'max_discount_amount' => 500000, 'status' => 'INACTIVE'],
            'terrace-lounge-chair' => ['value_type' => 'FIXED_AMOUNT', 'value' => 250000, 'max_discount_amount' => null, 'status' => 'ACTIVE'],
        ];

        $products = Product::query()
            ->whereIn('slug', array_keys($promos))
            ->get()
            ->keyBy('slug');

        if ($products->isEmpty()) {
            $this->command->warn('No furniture products found. Run ProductSeeder first.');

            return;
        }

        $startAt = now()->subDays(3)->startOfDay();
        $endAt = now()->addDays(30)->endOfDay();

        DB::transaction(function () use ($adminId, $promos, $products, $startAt, $endAt) {
            $upsert = function (array $key, array $values) {
                $discount = Discount::withTrashed()->updateOrCreate($key, $values);

                if ($discount->trashed()) {
                    $discount->restore();
                }

                return $discount;
            };

            foreach ($promos as $slug => $promo) {
                $product = $products->get($slug);

                if (! $product) {
                    continue;
                }

                $discount = $upsert(
                    ['code' => 'SEED_PROD_'.strtoupper(str_replace('-', '_', $slug))],
                    [
                        'name' => $product->name.' Promo',
                        'description' => 'Automatic promotional discount for '.$product->name.'.',
                        'level' => 'PRODUCT',
                        'target_type' => 'PRODUCT',
                        'value_type' => $promo['value_type'],
                        'value' => $promo['value'],
                        'max_discount_amount' => $promo['max_discount_amount'],
                        'min_purchase_amount' => 0,
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                        'usage_limit_total' => 50,
                        'usage_limit_per_user' => 1,
                        'is_stackable' => true,
                        'priority' => 1,
                        'status' => $promo['status'],
                        'created_by' => $adminId,
                    ]
                );

                $discount->discountTargets()->delete();
                $discount->discountTargets()->create([
                    'target_type' => 'PRODUCT',
                    'target_id' => $product->id,
                ]);
            }

            $shared = $upsert(
                ['code' => 'SEED_SHARED_MULTI'],
                [
                    'name' => 'LuxSpace Multi-Product Promo',
                    'description' => 'Save 15% when you purchase selected furniture pieces together.',
                    'level' => 'PRODUCT',
                    'target_type' => 'PRODUCT',
                    'value_type' => 'PERCENTAGE',
                    'value' => 15,
                    'max_discount_amount' => 1000000,
                    'min_purchase_amount' => 1000000,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'usage_limit_total' => 100,
                    'usage_limit_per_user' => 1,
                    'is_stackable' => false,
                    'priority' => 10,
                    'status' => 'ACTIVE',
                    'created_by' => $adminId,
                ]
            );

            $shared->discountTargets()->delete();

            foreach (['oslo-3-seater-sofa', 'oak-dining-table', 'ergo-office-chair'] as $slug) {
                $product = $products->get($slug);

                if (! $product) {
                    continue;
                }

                $shared->discountTargets()->create([
                    'target_type' => 'PRODUCT',
                    'target_id' => $product->id,
                ]);
            }
        });

        $this->command->info('11 furniture discounts seeded successfully.');
    }
}
