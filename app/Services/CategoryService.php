<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
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

    public function findByIdWithPosts(int $id): ?Category
    {
        return $this->categoryRepository->findByIdWithPosts($id);
    }

    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function getAvailableCategories(): Collection
    {
        return $this->categoryRepository->getAvailableCategories();
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        return $this->categoryRepository->updateCategory($id, $data);
    }

    /**
     * @throws Exception
     */
    public function deleteCategory(int $id): bool
    {
        if ($this->categoryRepository->categoryHasPosts($id)) {
            throw new Exception('Невозможно удалить категорию с существующими статьями');
        }

        return $this->categoryRepository->deleteCategory($id);
    }
}
