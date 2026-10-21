<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountTargetSeeder extends Seeder
{
    public function run(): void
    {
        $targets = [
            'SEED_CAT_LIVING_ROOM' => 'living-room',
            'SEED_CAT_BEDROOM' => 'bedroom',
            'SEED_CAT_DINING_ROOM' => 'dining-room',
            'SEED_CAT_HOME_OFFICE' => 'home-office',
            'SEED_CAT_OUTDOOR' => 'outdoor',
        ];

        $discounts = Discount::withTrashed()
            ->whereIn('code', array_keys($targets))
            ->get()
            ->keyBy('code');

        $categories = Category::query()
            ->whereIn('slug', array_values($targets))
            ->pluck('id', 'slug');

        if ($discounts->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Missing category discounts or categories. Run CategorySeeder and CategoryDiscountSeeder first.');

            return;
        }

        $created = 0;

        DB::transaction(function () use ($targets, $discounts, $categories, &$created) {
            foreach ($targets as $code => $categorySlug) {
                $discount = $discounts->get($code);
                $categoryId = $categories->get($categorySlug);

                if (! $discount || ! $categoryId) {
                    continue;
                }

                if ($discount->trashed()) {
                    $discount->restore();
                }

                $discount->discountTargets()->delete();

                $discount->discountTargets()->create([
                    'target_type' => 'CATEGORY',
                    'target_id' => $categoryId,
                ]);

                $created++;
            }
        });

        $this->command->info("{$created} category discount targets seeded successfully.");
    }
}
