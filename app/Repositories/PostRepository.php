<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
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

    public function createPost(array $data, User $user, Category $category): Post
    {
        return Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'short_description' => $data['short_description'] ?? null,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'published' => $data['published'] ?? false,
            'published_at' => ($data['published'] ?? false) ? now() : null,
        ]);
    }

    public function updatePost(Post $post, array $data): ?Post
    {
        $post->update($data);

        return $post->fresh();
    }

    public function deletePost(Post $post): bool
    {
        return $post->delete();
    }
}
