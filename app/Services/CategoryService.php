<?php

namespace App\Services;

use App\Dto\CategoryDto;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}

    public function getAllWithPostCount(): Collection
    {
        return $this->categoryRepository->getAllWithPostCount();
    }

    public function findByIdWithPosts(int $categoryId): ?Category
    {
        return $this->categoryRepository->findByIdWithPosts($categoryId);
    }

    public function createCategory(CategoryDto $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(Category $category, CategoryDto $data): Category
    {
        return $this->categoryRepository->updateCategory($category, $data);
    }

    public function deleteCategory(Category $category): bool
    {
        abort_if($this->categoryRepository->categoryHasPosts($category->id), 500);

        return $this->categoryRepository->deleteCategory($category);
    }
}
