<?php 

namespace App\Actions\Frontend;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Actions\Frontend\GetProduct;
use App\Actions\Frontend\AddToCart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;

Class GetCart
{
    public function handleGetCart()
    {
        if (Auth::check()) {

            // cek apakah ada session cart active
            $activeCartSession = $this->getActiveCartSession();

            if ($activeCartSession) {
                // jika ada, tambahkan ke database
                foreach ($activeCartSession as $productId => $product) {
                    app(AddToCart::class)->handleAddToCart($productId, $product['quantity']);
                }

                // hapus session cart setelah dipindahkan ke database
                Session::forget('cart');
            }

            $cart = Cart::with(['cartItems.product.galleries', 'cartItems.product.category'])->where('user_id', Auth::id())->first();
            $cartItems = [];

            // Tambahkan pengecekan apakah $cart tidak null sebelum memanggil cartItems()
            if ($cart && $cart->cartItems()->exists()) {
                foreach ($cart->cartItems as $item) {
                    $cartItems[] = [
                        'cart_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'product' => $item->product,
                        'weight' => $item->product->weight, // Pastikan $item->product juga tidak null
                    ];
                }
            }

            $cartItems = $this->applyPricing($cartItems)->all();

            Log::info('Cart items for user ' . Auth::id() . ': ' . json_encode($cartItems));
            return $cartItems;
        } else {
            $cartSession = Session::get('cart', []);
            // dd($cartSession);
            $cartItems = [];
            foreach ($cartSession as $productId => $product) {
                $cartItems[] = [
                    'product_id' => $productId,
                    'quantity' => $product['quantity'],
                    'product' => app(GetProduct::class)->getProductById($productId),
                ];  
            }
            return $this->applyPricing($cartItems);
        }
    }

    private function getActiveCartSession()
    {
        return Session::get('cart', []);
    }

    public function calculateTotalPriceCart()
    {
        return collect($this->handleGetCart())
            ->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));
    }

    private function applyPricing($cartItems)
    {
        $items = collect($cartItems);
        $products = $items->pluck('product')->filter()->values();

        $purchaseSubtotal = $items->sum(
            fn ($item) => (float) ($item['product']?->price ?? 0) * (int) ($item['quantity'] ?? 0)
        );

        if ($products->isNotEmpty()) {
            app(GetActiveProductDiscounts::class)->execute($products, (float) $purchaseSubtotal);
        }

        return $items->map(function ($item) {
            $product = $item['product'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 0);
            $originalPrice = (float) ($product?->price ?? 0);
            $unitPrice = (float) ($product?->final_price ?? $originalPrice);
            $discountAmount = (float) ($product?->discount_amount ?? 0);

            $item['original_price'] = $originalPrice;
            $item['unit_price'] = $unitPrice;
            $item['discount_amount'] = $discountAmount;
            $item['line_discount'] = $discountAmount * $quantity;
            $item['subtotal'] = $unitPrice * $quantity;

            return $item;
        });
    }

    public function decreaseQuantity($productId)
    {
        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('product_id', $productId)->first();
            if ($cartItem && $cartItem->quantity > 1) {
                $cartItem->quantity -= 1;
                $cartItem->save();
            } elseif ($cartItem && $cartItem->quantity == 1) {
                $cartItem->delete();
            }
        } else {
            $cartSession = Session::get('cart', []);
            if (isset($cartSession[$productId]) && $cartSession[$productId]['quantity'] > 1) {
                $cartSession[$productId]['quantity'] -= 1;
                Session::put('cart', $cartSession);
            }
        }
    }

    public function increaseQuantity($productId)
    {
        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('product_id', $productId)->first();
            if ($cartItem) {
                $cartItem->quantity += 1;
                $cartItem->save();
            }
        } else {
            $cartSession = Session::get('cart', []);
            if (isset($cartSession[$productId])) {
                $cartSession[$productId]['quantity'] += 1;
                Session::put('cart', $cartSession);
            }
        }
    }

    public function deleteItemCart($productId)
    {
        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('product_id', $productId)->first();
            if ($cartItem) {
                $cartItem->delete();
            }
        } else {
            $cartSession = Session::get('cart', []);
            if (isset($cartSession[$productId])) {
                unset($cartSession[$productId]);
                Session::put('cart', $cartSession);
            }
        }
    }
}