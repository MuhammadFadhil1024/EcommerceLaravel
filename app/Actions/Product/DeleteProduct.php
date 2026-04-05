<?php 

namespace App\Actions\Product;

use App\Models\Product;

Class DeleteProduct
{
    public function delete(int $productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();
    }
}