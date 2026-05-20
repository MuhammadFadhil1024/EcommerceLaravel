<?php

namespace App\Actions\Frontend;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveTransaction
{
    public function handleSaveTransaction(array $dataPayment, array $xenditPayment): Transaction
    {
        $cartItems = $dataPayment['cart_items'] ?? [];
        $userId = (int) ($dataPayment['user_id'] ?? Auth::id());
        $addressId = (int) ($dataPayment['address_id'] ?? 0);

        if ($userId <= 0) {
            throw new Exception('User tidak ditemukan.');
        }

        if ($addressId <= 0) {
            throw new Exception('Alamat utama belum dipilih.');
        }

        if (empty($cartItems)) {
            throw new Exception('Cart kosong, tidak ada item yang bisa disimpan.');
        }

        try {
            return DB::transaction(function () use ($dataPayment, $xenditPayment, $cartItems, $userId, $addressId) {
                $transaction = Transaction::create([
                    'user_id' => $userId,
                    'reference_id' => $xenditPayment['reference_id'] ?? '',
                    'session_id' => $xenditPayment['payment_session_id'] ?? '',
                    'address_id' => $addressId,
                    'total_payment' => (float) ($xenditPayment['amount'] ?? $dataPayment['total_payment'] ?? 0),
                    'courier_cost' => (float) ($dataPayment['courier_cost'] ?? 0),
                    'courier' => $dataPayment['courier_service'] ?? '',
                    'payment_date' => now(),
                    'expires_at' => null,
                    'payment_method' => 'Xendit',
                    'payment_url' => $xenditPayment['payment_link_url'] ?? '',
                    'status' => 'pending',
                ]);

                $transactionItems = [];
                foreach ($cartItems as $item) {
                    $productId = (int) ($item['product_id'] ?? ($item['product']->id ?? 0));
                    $quantity = (int) ($item['quantity'] ?? 0);
                    $price = (float) ($item['product']->price ?? 0);
                    $totalPrice = $quantity * $price;

                    if ($productId <= 0 || $quantity <= 0) {
                        continue;
                    }

                    $transactionItems[] = [
                        'transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'user_id' => $userId,
                        'quantity' => $quantity,
                        'total_price' => $totalPrice,
                    ];
                }

                if (empty($transactionItems)) {
                    throw new Exception('Tidak ada item valid untuk disimpan.');
                }

                TransactionItem::insert($transactionItems);

                $this->clearUserCart($userId);

                return $transaction;
            });
        } catch (Exception $e) {
            Log::error('Save transaction failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dataPayment' => $dataPayment,
                'xenditPayment' => $xenditPayment,
            ]);

            throw new Exception('Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    protected function clearUserCart(int $userId): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->cartItems()->delete();
            $cart->delete();
        }
    }
}
