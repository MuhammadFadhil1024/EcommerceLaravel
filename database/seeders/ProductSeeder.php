<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'living-room',
                'name' => 'Oslo 3-Seater Sofa',
                'slug' => 'oslo-3-seater-sofa',
                'description' => 'A spacious three-seater sofa with plush cushioning and a clean Scandinavian silhouette, upholstered in soft woven fabric.',
                'price' => 7500000,
                'stock' => 8,
                'weight' => 45000,
            ],
            [
                'category' => 'living-room',
                'name' => 'Luna Coffee Table',
                'slug' => 'luna-coffee-table',
                'description' => 'A round coffee table with a tempered glass top and slender powder-coated legs, perfect for a modern living room.',
                'price' => 2200000,
                'stock' => 15,
                'weight' => 18000,
            ],
            [
                'category' => 'bedroom',
                'name' => 'Aurora Queen Bed',
                'slug' => 'aurora-queen-bed',
                'description' => 'A queen-size platform bed with an upholstered headboard and solid wood slats for a restful night of sleep.',
                'price' => 6800000,
                'stock' => 6,
                'weight' => 55000,
            ],
            [
                'category' => 'bedroom',
                'name' => 'Haven Wardrobe',
                'slug' => 'haven-wardrobe',
                'description' => 'A three-door wardrobe with adjustable shelves, a hanging rail, and a soft-close mechanism.',
                'price' => 4900000,
                'stock' => 10,
                'weight' => 70000,
            ],
            [
                'category' => 'dining-room',
                'name' => 'Oak Dining Table',
                'slug' => 'oak-dining-table',
                'description' => 'A solid oak dining table with a natural finish, seating up to six guests comfortably.',
                'price' => 5600000,
                'stock' => 7,
                'weight' => 40000,
            ],
            [
                'category' => 'dining-room',
                'name' => 'Velvet Dining Chair',
                'slug' => 'velvet-dining-chair',
                'description' => 'A dining chair with a padded velvet seat, curved backrest, and sturdy metal legs.',
                'price' => 1450000,
                'stock' => 24,
                'weight' => 8000,
            ],
            [
                'category' => 'home-office',
                'name' => 'Ergo Office Chair',
                'slug' => 'ergo-office-chair',
                'description' => 'An ergonomic office chair with adjustable lumbar support, armrests, and a breathable mesh back.',
                'price' => 2750000,
                'stock' => 12,
                'weight' => 15000,
            ],
            [
                'category' => 'home-office',
                'name' => 'Walnut Writing Desk',
                'slug' => 'walnut-writing-desk',
                'description' => 'A compact writing desk in walnut veneer with two drawers and a cable management cutout.',
                'price' => 3800000,
                'stock' => 9,
                'weight' => 28000,
            ],
            [
                'category' => 'outdoor',
                'name' => 'Teak Patio Set',
                'slug' => 'teak-patio-set',
                'description' => 'A four-piece teak patio set with a table and three chairs, treated for outdoor weather resistance.',
                'price' => 9200000,
                'stock' => 4,
                'weight' => 60000,
            ],
            [
                'category' => 'outdoor',
                'name' => 'Terrace Lounge Chair',
                'slug' => 'terrace-lounge-chair',
                'description' => 'A weather-resistant lounge chair with a woven seat and a powder-coated aluminium frame.',
                'price' => 1900000,
                'stock' => 14,
                'weight' => 12000,
            ],
        ];

        $categoryIds = Category::query()
            ->whereIn('slug', array_unique(array_column($products, 'category')))
            ->pluck('id', 'slug');

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $categoryIds[$product['category']],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'weight' => $product['weight'],
                    'is_available' => true,
                ]
            );
        }

        $this->command->info('10 furniture products seeded successfully.');
    }
}
