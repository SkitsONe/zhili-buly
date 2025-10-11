<?php

namespace App\Repositories;

use App\Dto\CategoryDto;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

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

    public function create(CategoryDto $data): Category
    {
        return Category::create($data->toArray());
    }

    public function updateCategory(Category $category, CategoryDto $data): Category
    {
        $category->update($data->toArray());
        return $category->fresh();
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }

    public function categoryHasPosts(int $id): bool
    {
        return Category::where('id', $id)
            ->whereHas('posts')
            ->exists();
    }
}
