<?php 

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\GifEncoder;
use Intervention\Image\ImageManager;

Class UpdateCategory
{
    private function generateSlug(string $name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        return $slug;
    }

    public function update(int $categoryId, array $data)
    {
        $category = Category::findOrFail($categoryId);

        if ($data['thumbnail'] instanceof \Illuminate\Http\UploadedFile && $data['thumbnail']) {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($data['thumbnail']);
            $webpThumbnail = $image->encode(new GifEncoder());

            $fileName = uniqid() . '.webp';
            $path = 'category/' . $fileName;

            Storage::disk('public')->put($path, $webpThumbnail);

            if ($category->thumbnail) {
                Storage::disk('public')->delete($category->thumbnail);
            }

            $data['thumbnail'] = $path;
        } else {
            unset($data['thumbnail']);
        }

        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);

            if (Category::where('slug', $data['slug'])->where('id', '!=', $categoryId)->exists()) {
            
            throw ValidationException::withMessages([
                'name' => 'Category with the same name already exists.'
            ]);
    }
        }

            $category->fill($data);
            $category->save();
            return $category;

    }
}