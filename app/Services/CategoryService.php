<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}

    public function getAllWithPostCount()
    {
        return $this->categoryRepository->getAllWithPostCount();
    }

    public function findByIdWithPosts($id): ?Category
    {
        return $this->categoryRepository->findByIdWithPosts($id);
    }

    public function findById($id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function getAvailableCategories()
    {
        return $this->categoryRepository->getAvailableCategories();
    }

    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);
    }

    public function updateCategory($id, array $data): ?Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return null;
        }

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return $category->fresh();
    }

    public function deleteCategory($id): bool
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return false;
        }

        if ($category->posts()->count() > 0) {
            throw new \Exception('Невозможно удалить категорию с существующими статьями');
        }

        return $category->delete();
    }
}
