<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

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

    public function findByIdWithRelations(int $id): ?Post
    {
        return Post::with(['user', 'category'])->find($id);
    }

    public function findById(int $id): ?Post
    {
        return Post::find($id);
    }

    public function getUserPosts(int $userId)
    {
        return Post::where('user_id', $userId)
            ->with(['category'])
            ->latest()
            ->get();
    }

    public function createPost(array $data, User $user, Category $category): Post
    {
        return Post::create([
            'title' => $data['title'],
            'slug' => $this->generateSlug($data['title']),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'published' => $data['published'] ?? false,
            'published_at' => ($data['published'] ?? false) ? now() : null,
        ]);
    }

    public function updatePost($id, array $data): ?Post
    {
        $post = $this->findById($id);

        if (isset($data['title']) && $post->title !== $data['title']) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        $post->update($data);
        return $post->fresh();
    }

    public function deletePost(int $id): bool
    {
        $post = $this->findById($id);

        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    public function checkOwnership(int $postId, int $userId): bool
    {
        $post = $this->findById($postId);
        return $post && $post->user_id === $userId;
    }

    private function generateSlug(string $title): string
    {
        return Str::slug($title) . '-' . uniqid();
    }
}
