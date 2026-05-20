<?php

namespace App\Actions\Frontend;

use Illuminate\Support\Facades\DB;
use Exception;

class DecreaseStock
{
    public function handleDecreaseStock($transaction): void
    {
        // 1. Validasi awal sebelum masuk ke transaksi database
        if (!$transaction || !$transaction->items()->exists()) {
            throw new Exception('Produk tidak ditemukan.');
        }

        // 2. Bungkus SELURUH proses pengecekan dan pengurangan stok dalam satu transaksi
        DB::transaction(function () use ($transaction) {
            
            foreach ($transaction->items as $item) {
                
                // Gunakan kurung () pada product() untuk memanggil Query Builder, bukan property
                $product = $item->product()->lockForUpdate()->first();
    
                if (!$product) {
                    throw new Exception('Produk tidak ditemukan untuk item dengan ID: ' . $item->id);
                }

                $quantity = $item->quantity;

                if ($product->stock < $quantity) {
                    throw new Exception('Stok tidak cukup untuk produk: ' . $product->name);
                }
    
                // Kurangi stok dan simpan (karena row ini sudah aman terkunci)
                $product->stock -= $quantity;
                $product->save();
            }

        }); // Jika ada Exception di dalam loop, SELURUH perubahan stok akan di-rollback otomatis
    }
}