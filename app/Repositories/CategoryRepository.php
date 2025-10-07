<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryRepository
{
    public function getAllWithPostCount(): Collection
    {
        return Category::withCount('posts')->get();
    }

    public function findByIdWithPosts(int $id): ?Category
    {
        return Category::with(['posts.user'])->find($id);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function getAvailableCategories(): Collection
    {
        return Category::all(['id', 'name', 'slug']);
    }

    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        $category = $this->findById($id);


        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return $category->fresh();
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->findById($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    public function categoryHasPosts(int $id): bool
    {
        $category = $this->findById($id);
        return $category && $category->posts()->count() > 0;
    }

    public function findOrCreateCategory(array $data): Category
    {
        if (isset($data['category_id'])) {
            return Category::firstOrCreate(
                ['id' => $data['category_id']],
                [
                    'name' => $data['category_name'] ?? 'Категория ' . $data['category_id'],
                    'slug' => Str::slug($data['category_name'] ?? 'category-' . $data['category_id'])
                ]
            );
        }

        if (!empty($data['category_name'])) {
            return Category::firstOrCreate(
                ['name' => $data['category_name']],
                [
                    'slug' => Str::slug($data['category_name'])
                ]
            );
        }

        return Category::firstOrCreate(
            ['name' => 'Общая'],
            [
                'slug' => 'general'
            ]
        );
    }
}
