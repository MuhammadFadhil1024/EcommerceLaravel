<?php

namespace App\Actions\Frontend;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class Checkout
{
    /**
     * Mengeksekusi pengambilan semua product.
     *
     * @return Collection
     */
    public function handleCheckout()
    {
        $cart = app(GetCart::class)->handleGetCart();

        $cartItems = [];
        $grandTotal = 0;
        $originalGrandTotal = 0;
        $totalDiscount = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $quantity = (int) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $item['product']->price ?? 0);
            $subtotal = (float) ($item['subtotal'] ?? ($quantity * $unitPrice));
            $originalPrice = (float) ($item['original_price'] ?? $item['product']->price ?? $unitPrice);
            $discountAmount = (float) ($item['discount_amount'] ?? 0);
            $lineDiscount = (float) ($item['line_discount'] ?? ($discountAmount * $quantity));
            $originalSubtotal = $originalPrice * $quantity;

            $cartItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'original_price' => $originalPrice,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'line_discount' => $lineDiscount,
                'original_subtotal' => $originalSubtotal,
                'subtotal' => $subtotal,
                'product' => $item['product'],
            ];

            $grandTotal += $subtotal;
            $originalGrandTotal += $originalSubtotal;
            $totalDiscount += $lineDiscount;
            $totalItems += $quantity;
        }

        return [
            'Cart' => $cartItems,
            'original_grand_total' => $originalGrandTotal,
            'total_discount' => $totalDiscount,
            'grand_total' => $grandTotal,
            'total_items' => $totalItems,
        ];
    }
}
