<?php 

namespace App\Actions\Product;

use App\Models\Product;

Class GetProduct
{

    /**
     * Mengeksekusi pengambilan semua product.
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getAllProducts()
    {
        $products = Product::query()->paginate(10);

        return $products;
    }


    public function getProductById(int $productId): ?Product
    {
        return Product::findOrFail($productId);
    }
}