<?php 

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\GifEncoder;
use Intervention\Image\ImageManager;
use Illuminate\Validation\ValidationException;

Class CreateNewCategory
{
    /**
     * Mengeksekusi pembuatan kategori baru.
     * @return \Illuminate\Database\Eloquent\Collection
     */

    private function generateSlug(string $name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        return $slug;
    }

    public function create(array $data)
    {
        if ($data['thumbnail'] instanceof \Illuminate\Http\UploadedFile && $data['thumbnail']) {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($data['thumbnail']);
            $webpThumbnail = $image->encode(new GifEncoder());

            $fileName = uniqid() . '.webp';
            $path = 'category/' . $fileName;

            Storage::disk('public')->put($path, $webpThumbnail);

            $data['thumbnail'] = $path;
        }

        $data['slug'] = $this->generateSlug($data['name']);

        if (Category::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Category with the same name already exists.'
            ]);
        }

        $category = new Category();
        $category->fill($data);
        $category->save();
        return $category;
        
    }
}