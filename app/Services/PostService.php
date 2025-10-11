<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository
    ) {}

    public function getAllWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->postRepository->getAllWithFilters($filters);
    }

    public function findByIdWithRelations(int $postId): ?Post
    {
        return $this->postRepository->findByIdWithRelations($postId);
    }

    public function createPost(array $data, User $user): Post
    {
        $category = $this->resolveCategory($data);

        return $this->postRepository->createPost($data, $user, $category);
    }

    public function updatePost(Post $post, array $data): ?Post
    {
        return $this->postRepository->updatePost($post, $data);
    }

    public function deletePost(Post $post): bool
    {
        return $this->postRepository->deletePost($post);
    }

    private function resolveCategory(array $data): Category
    {
        return $this->categoryRepository->findOrCreateCategory($data);
    }
}
