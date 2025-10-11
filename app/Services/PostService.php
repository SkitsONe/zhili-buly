<?php

namespace App\Services;

use App\Dto\PostDto;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        private PostRepository $postRepository
    ) {}

    public function getAllWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->postRepository->getAllWithFilters($filters);
    }

    public function findByIdWithRelations(int $postId): ?Post
    {
        return $this->postRepository->findByIdWithRelations($postId);
    }

    public function createPost(PostDto $data): Post
    {
        abort_if($data->categoryId && !$this->postRepository->categoryExists($data->categoryId), 422);

        return $this->postRepository->createPost($data);
    }

    public function updatePost(Post $post, PostDto $data): Post
    {
        abort_if($data->categoryId && !$this->postRepository->categoryExists($data->categoryId), 422);

        return $this->postRepository->updatePost($post, $data);
    }

    public function deletePost(Post $post): bool
    {
        return $this->postRepository->deletePost($post);
    }
}
