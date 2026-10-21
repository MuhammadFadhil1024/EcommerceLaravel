<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Living Room', 'slug' => 'living-room'],
            ['name' => 'Bedroom', 'slug' => 'bedroom'],
            ['name' => 'Dining Room', 'slug' => 'dining-room'],
            ['name' => 'Home Office', 'slug' => 'home-office'],
            ['name' => 'Outdoor', 'slug' => 'outdoor'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('5 furniture categories seeded successfully.');
    }
}
