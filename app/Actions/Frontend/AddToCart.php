<?php 

namespace App\Actions\Frontend;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

Class AddToCart
{

   private function saveToDatabase(int $productId, int $quantity)
    {
        DB::transaction(function () use ($productId, $quantity) {
            // 1. Cari keranjang user, jika tidak ada langsung buatkan otomatis
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            // 2. Cek apakah produk sudah ada di cart_items
            $cartItem = $cart->cartItems()->where('product_id', $productId)->first();

            if ($cartItem) {
                // 3a. Jika ada, gunakan fungsi 'increment' agar query langsung dieksekusi di database (Sangat Cepat & Atomic)
                $cartItem->increment('quantity', $quantity);
            } else {
                // 3b. Jika tidak ada, buat baru
                $cart->cartItems()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
        });
    }

    private function saveToSession(int $productId, int $quantity)
    {
        $cart = Session::get('cart', []);
        // jika produk sudah ada di cart, tambahkan quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'productId' => $productId,
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);
    }

    public function handleAddToCart(int $productId, int $quantity)
    {
        // cek apakah user login atau tidak
        if (Auth::check()) { // store ke db
            $this->saveToDatabase($productId, $quantity);
        } else { // store ke session
            $this->saveToSession($productId, $quantity);
        }
    }
}