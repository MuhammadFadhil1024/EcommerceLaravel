<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->value('id') ?? User::factory()->create([
            'name' => 'Admin Seeder',
            'email' => 'admin-seeder@example.com',
        ])->id;

        $promos = [
            'living-room' => [
                'name' => 'Living Room Sale',
                'description' => 'Save 10% on all living room furniture.',
                'value_type' => 'PERCENTAGE',
                'value' => 10,
                'max_discount_amount' => 500000,
                'min_purchase_amount' => 0,
                'priority' => 5,
            ],
            'bedroom' => [
                'name' => 'Bedroom Special',
                'description' => 'Get Rp 300.000 off every bedroom furniture purchase.',
                'value_type' => 'FIXED_AMOUNT',
                'value' => 300000,
                'max_discount_amount' => null,
                'min_purchase_amount' => 2000000,
                'priority' => 4,
            ],
            'dining-room' => [
                'name' => 'Dining Room Deals',
                'description' => 'Save 15% on selected dining room furniture.',
                'value_type' => 'PERCENTAGE',
                'value' => 15,
                'max_discount_amount' => 750000,
                'min_purchase_amount' => 0,
                'priority' => 6,
            ],
            'home-office' => [
                'name' => 'Home Office Promo',
                'description' => 'Get Rp 250.000 off every home office furniture purchase.',
                'value_type' => 'FIXED_AMOUNT',
                'value' => 250000,
                'max_discount_amount' => null,
                'min_purchase_amount' => 1500000,
                'priority' => 3,
            ],
            'outdoor' => [
                'name' => 'Outdoor Season Sale',
                'description' => 'Save 20% on all outdoor furniture.',
                'value_type' => 'PERCENTAGE',
                'value' => 20,
                'max_discount_amount' => 1000000,
                'min_purchase_amount' => 0,
                'priority' => 7,
            ],
        ];

        $startAt = now()->subDays(3)->startOfDay();
        $endAt = now()->addDays(30)->endOfDay();

        DB::transaction(function () use ($adminId, $promos, $startAt, $endAt) {
            foreach ($promos as $slug => $promo) {
                $code = 'SEED_CAT_'.strtoupper(str_replace('-', '_', $slug));

                $discount = Discount::withTrashed()->updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $promo['name'],
                        'description' => $promo['description'],
                        'level' => 'PRODUCT',
                        'target_type' => 'CATEGORY',
                        'value_type' => $promo['value_type'],
                        'value' => $promo['value'],
                        'max_discount_amount' => $promo['max_discount_amount'],
                        'min_purchase_amount' => $promo['min_purchase_amount'],
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                        'usage_limit_total' => 100,
                        'usage_limit_per_user' => 1,
                        'is_stackable' => true,
                        'priority' => $promo['priority'],
                        'status' => 'ACTIVE',
                        'created_by' => $adminId,
                    ]
                );

                if ($discount->trashed()) {
                    $discount->restore();
                }
            }
        });

        $this->command->info('5 category-targeted discounts seeded successfully.');
    }
}
