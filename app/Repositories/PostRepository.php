<?php

namespace App\Repositories;

use App\Dto\PostDto;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    private const PEERPAGE = 10;

    public function getAllWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with(['user', 'category']);

        if (isset($filters['category'])) {
            $query->whereHas('category', function($query) use ($filters) {
                $query->where('slug', $filters['category']);
            });
        }

        if (isset($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }

        return $query->latest()->paginate($this::PEERPAGE);
    }

    public function findByIdWithRelations(int $postId): ?Post
    {
        return Post::with(['user', 'category'])->find($postId);
    }

    public function createPost(PostDto $data): Post
    {
        return Post::create($data->toArray());
    }

    public function updatePost(Post $post, PostDto $data): ?Post
    {
        $post->update($data->toArray());

        return $post->fresh();
    }

    public function deletePost(Post $post): bool
    {
        return $post->delete();
    }

    public function categoryExists(int $categoryId): bool
    {
        return Category::where('id', $categoryId)->exists();
    }
}
