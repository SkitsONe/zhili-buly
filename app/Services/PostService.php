<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;

class PostService
{
    public function __construct(
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository
    ) {}

    public function getAllWithFilters(array $filters = [])
    {
        return $this->postRepository->getAllWithFilters($filters);
    }

    public function findByIdWithRelations(int $id): ?Post
    {
        return $this->postRepository->findByIdWithRelations($id);
    }

    public function findById(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    public function getUserPosts(int $userId)
    {
        return $this->postRepository->getUserPosts($userId);
    }

    public function createPost(array $data, User $user): Post
    {
        return $this->postRepository->createPost($data, $user);

    }

    public function updatePost(int $id, array $data): ?Post
    {
        return $this->postRepository->updatePost($id, $data);
    }

    public function deletePost(int $id): bool
    {
        return $this->postRepository->deletePost($id);
    }

    public function checkOwnership(int $postId, int $userId): bool
    {
        return $this->postRepository->checkOwnership($postId, $userId);
    }

    private function resolveCategory(array $data): Category
    {
        return $this->categoryRepository->findOrCreateCategory($data);
    }
}
