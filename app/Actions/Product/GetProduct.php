<?php 

namespace App\Actions\Product;

use App\Models\Product;
use App\Models\ProductGallery;

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

    public function getProductImages(int $productId)
    {
        $productImage = ProductGallery::where('product_id', $productId)->get();
        return $productImage;
    }
}