<?php 

namespace App\Actions\Frontend;

use App\Models\Product;
use App\Actions\Frontend\GetCart;

Class Checkout
{

    /**
     * Mengeksekusi pengambilan semua product.
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function handleCheckout()
    {
        $Cart = app(GetCart::class)->handleGetCart();

        $DataCheckout = [];
        $grandTotal = 0;
        $totalItems = 0;
        foreach ($Cart as $item) {
            $DataCheckout[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['quantity'] * $item['product']->price,
                'product' => $item['product'],
                $grandTotal += $item['quantity'] * $item['product']->price,
                $totalItems += $item['quantity'],
            ];
        }
        $DataCheckout['Cart'] = $DataCheckout;
        $DataCheckout['grand_total'] = $grandTotal;
        $DataCheckout['total_items'] = $totalItems;
        // dd($DataCheckout);

        return $DataCheckout;
    }
}