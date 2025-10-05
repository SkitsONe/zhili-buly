<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function getAllWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with(['user', 'category']);

        if (isset($filters['category'])) {
            $query->whereHas('category', function($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (isset($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }

        return $query->latest()->paginate(10);
    }

    public function findByIdWithRelations($id): ?Post
    {
        return Post::with(['user', 'category'])->find($id);
    }

    public function findById($id): ?Post
    {
        return Post::find($id);
    }

    public function getUserPosts($userId)
    {
        return Post::where('user_id', $userId)
            ->with(['category'])
            ->latest()
            ->get();
    }
}
