<?php 

namespace App\Actions\Frontend;

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
        $products = Product::with(['galleries'])->get();

        return $products;
    }

    public function getProductBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->with(['galleries'])->firstOrFail();
    }

    public function getRelatedProductsByCategoryId(int $categoryId, int $excludeProductId)
    {
        return Product::where('category_id', $categoryId)
            ->where('id', '!=', $excludeProductId)
            ->with(['galleries'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    public function getProductById(int $productId): ?Product
    {
        return Product::find($productId);
    }


    // public function getProductById(int $productId): ?Product
    // {
    //     return Product::findOrFail($productId);
    // }

    // public function getProductImages(int $productId)
    // {
    //     $productImage = ProductGallery::where('product_id', $productId)->get();
    //     return $productImage;
    // }
}