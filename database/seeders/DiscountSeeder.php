<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure at least 1 user exists for created_by (FK constraint).
        // Use the first existing user if there is one; otherwise create a dummy user.
        $adminId = User::query()->value('id') ?? User::factory()->create([
            'name' => 'Admin Seeder',
            'email' => 'admin-seeder@example.com',
        ])->id;

        $names = [
            'Independence Day Sale', 'Year End Flash Sale', 'New Customer Promo',
            'Payday Discount', 'Online Shopping Day Special', 'Store Anniversary Discount',
            'Ramadan Promo', 'Christmas & New Year Discount', 'Weekend Cashback',
            'New Member Discount', 'National Online Shopping Day', '12PM Flash Sale',
            'Best Seller Discount', 'Bundle Saver Promo', 'Reseller Exclusive Discount',
            'Clearance Sale', 'New Arrival Discount', 'Double Date Promo',
            'Weekend Discount', 'Seasonal Changeover Sale',
        ];

        foreach ($names as $i => $name) {
            // Alternate level: PRODUCT or TRANSACTION
            $level = $i % 2 === 0 ? 'PRODUCT' : 'TRANSACTION';

            // Alternate value type: PERCENTAGE or FIXED_AMOUNT
            $valueType = $i % 3 === 0 ? 'FIXED_AMOUNT' : 'PERCENTAGE';

            // Value must respect the chk_percent / chk_value constraints
            $value = $valueType === 'PERCENTAGE'
                ? fake()->numberBetween(5, 50)          // percentage, safely below 100
                : fake()->numberBetween(10000, 150000);  // fixed amount in Rupiah

            // max_discount_amount only makes sense for PERCENTAGE
            $maxDiscount = $valueType === 'PERCENTAGE'
                ? fake()->numberBetween(20000, 100000)
                : null;

            // min_purchase_amount is more relevant for TRANSACTION level
            $minPurchase = $level === 'TRANSACTION'
                ? fake()->randomElement([0, 100000, 250000, 500000])
                : 0;

            // Mix of past, ongoing, and upcoming periods,
            // while always keeping end_at > start_at (chk_period constraint)
            $startAt = Carbon::now()->subDays(fake()->numberBetween(-30, 30));
            $endAt = (clone $startAt)->addDays(fake()->numberBetween(7, 60));

            // Alternate status so the status filter can be tested with every option
            $status = match ($i % 3) {
                0 => 'ACTIVE',
                1 => 'DRAFT',
                default => 'INACTIVE',
            };

            Discount::create([
                'name' => $name,
                'description' => fake()->sentence(10),
                'code' => $level === 'TRANSACTION'
                    ? strtoupper(fake()->unique()->bothify('PROMO###'))
                    : null, // product-level discounts are usually automatic, no code needed
                'level' => $level,
                'value_type' => $valueType,
                'value' => $value,
                'max_discount_amount' => $maxDiscount,
                'min_purchase_amount' => $minPurchase,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'usage_limit_total' => fake()->optional(0.7)->numberBetween(50, 500),
                'usage_limit_per_user' => fake()->optional(0.5)->numberBetween(1, 5),
                'used_count' => 0,
                'is_stackable' => fake()->boolean(70),
                'priority' => fake()->numberBetween(0, 10),
                'status' => $status,
                'created_by' => $adminId,
            ]);
        }

        $this->command->info('20 discount records seeded successfully.');
    }
}