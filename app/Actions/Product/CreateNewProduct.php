<?php 

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

Class CreateNewProduct
{
    /**
     * Mengeksekusi pembuatan produk baru.
     * @return \Illuminate\Database\Eloquent\Collection
     */

    private function generateSlug(string $name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        return $slug;
    }

    private function cleaningPrice($price)
    {
        // Jika input kosong, kembalikan 0
        if (empty($price)) {
            return 0;
        }

        // Pastikan input adalah string sebelum diproses
        $price = (string) $price;

        // 1. Hapus semua karakter KECUALI angka (0-9), koma (,), dan titik (.)
        // Menghilangkan 'Rp', spasi, strip, dll.
        $cleaned = preg_replace('/[^0-9.,]/', '', $price);

        // 2. Proses format Indonesia (contoh: 1.500.000,50)
        // Cek apakah ada koma. Jika ya, berarti pengguna memasukkan nilai desimal (sen)
        if (str_contains($cleaned, ',')) {
            // Hapus semua titik (karena titik adalah pemisah ribuan)
            $cleaned = str_replace('.', '', $cleaned);
            
            // Ubah koma menjadi titik (karena Database & PHP butuh titik untuk desimal)
            $cleaned = str_replace(',', '.', $cleaned);
        } else {
            // Jika TIDAK ADA koma, berarti pengguna hanya mengetik ribuan biasa (contoh: 1.500.000)
            // Maka kita hapus saja semua titiknya agar menjadi angka bulat murni (1500000)
            $cleaned = str_replace('.', '', $cleaned);
        }

        // Casting (ubah tipe) ke float agar PHP mengenalinya sebagai angka desimal yang valid
        return (float) $cleaned;
    }

    public function create(array $data)
    {

        $data['slug'] = $this->generateSlug($data['name']);
        $data['price'] = $this->cleaningPrice($data['price']);

        if (Product::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Product with the same name already exists.'
            ]);
        }

        $product = new Product();
        $product->fill($data);
        $product->save();
        return $product;
        
    }
}