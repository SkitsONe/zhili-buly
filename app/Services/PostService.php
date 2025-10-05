<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Repositories\PostRepository;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        private PostRepository $postRepository
    ) {}

    public function getAllWithFilters(array $filters = [])
    {
        return $this->postRepository->getAllWithFilters($filters);
    }

    public function findByIdWithRelations($id): ?Post
    {
        return $this->postRepository->findByIdWithRelations($id);
    }

    public function findById($id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    public function getUserPosts($userId)
    {
        return $this->postRepository->getUserPosts($userId);
    }

    public function createPost(array $data, $user): Post
    {
        $category = $this->resolveCategory($data);

        return Post::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . uniqid(),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'published' => $data['published'] ?? false,
            'published_at' => ($data['published'] ?? false) ? now() : null,
        ]);
    }

    private function resolveCategory(array $data): Category
    {
        if (isset($data['category_id'])) {
            return Category::firstOrCreate(
                ['id' => $data['category_id']],
                [
                    'name' => 'Категория ' . $data['category_id'],
                    'slug' => 'category-' . $data['category_id']
                ]
            );
        }

        if (isset($data['category_name']) && !empty($data['category_name'])) {
            return Category::firstOrCreate(
                ['name' => $data['category_name']],
                [
                    'slug' => Str::slug($data['category_name'])
                ]
            );
        }

        return Category::firstOrCreate(
            ['name' => 'Общая'],
            [
                'slug' => 'general'
            ]
        );
    }

    public function updatePost($id, array $data): ?Post
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return null;
        }

        if (isset($data['category_id']) || isset($data['category_name'])) {
            $category = $this->resolveCategory($data);
            $data['category_id'] = $category->id;
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        $post->update($data);
        return $post->fresh();
    }

    public function deletePost($id): bool
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    public function checkOwnership($postId, $userId): bool
    {
        $post = $this->postRepository->findById($postId);
        return $post && $post->user_id === $userId;
    }
}
