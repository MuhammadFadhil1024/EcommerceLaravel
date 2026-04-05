<?php 

namespace App\Actions\Product;

use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\GifEncoder;
use Intervention\Image\ImageManager;

Class UpdateProduct
{
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

    public function update(int $productId, array $data)
    {
        $product = Product::findOrFail($productId);

        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);

            if (Product::where('slug', $data['slug'])->where('id', '!=', $productId)->exists()) {
            
            throw ValidationException::withMessages([
                'name' => 'Product with the same name already exists.'
            ]);
            }
        }

        if (isset($data['price'])) {
            $data['price'] = $this->cleaningPrice($data['price']);
        }

        $product->fill($data);
        $product->save();
        return $product;
    }

    public function addProductImage(int $productId, array $data)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($data['image']);
        $webpThumbnail = $image->encode(new GifEncoder());

        $fileName = uniqid() . '.webp';
        $path = 'productImages/' . $fileName;

        Storage::disk('public')->put($path, $webpThumbnail);

        $data['image'] = $path;
        $data['product_id'] = $productId;
        $productImage = new ProductGallery();
        $productImage->fill($data);
        $productImage->save();
        return $productImage;
    }

    /**
     * Mengatur gambar tertentu menjadi gambar utama (featured)
     */
    public function setAsFeatured(int $productId, int $imageId)
    {
        // 1. Reset semua gambar milik produk ini agar tidak ada yang featured
        ProductGallery::where('product_id', $productId)->update(['is_featured' => 0]);

        // 2. Set gambar yang dipilih menjadi featured
        ProductGallery::findOrFail($imageId)->update(['is_featured' => 1]);
    }

    /**
     * Menghapus gambar produk dari database dan storage
     */
    public function deleteProductImage(int $imageId)
    {
        $image = ProductGallery::findOrFail($imageId);

        // 1. Hapus file fisik dari storage lokal (jika file-nya ada)
        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        // 2. Hapus data dari database
        $image->delete();
    }
}