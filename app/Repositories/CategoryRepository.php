<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function getAllWithPostCount(): Collection
    {
        return Category::withCount('posts')->get();
    }

    public function findByIdWithPosts($id): ?Category
    {
        return Category::with(['posts.user'])->find($id);
    }

    public function findById($id): ?Category
    {
        return Category::find($id);
    }

    public function getAvailableCategories(): Collection
    {
        return Category::all(['id', 'name', 'slug']);
    }
}
