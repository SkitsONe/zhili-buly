<?php

namespace App\Dto;

class PostDto
{
    public function __construct(
        public string $title,
        public string $content,
        public int $userId,
        public ?string $shortDescription = null,
        public ?int $categoryId = null,
        public bool $published = false,
        public ?string $publishedAt = null
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            title: $data['title'],
            content: $data['content'],
            userId: $userId,
            shortDescription: $data['short_description'] ?? null,
            categoryId: $data['category_id'] ?? null,
            published: $data['published'] ?? false,
            publishedAt: $data['published_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'short_description' => $this->shortDescription,
            'category_id' => $this->categoryId,
            'published' => $this->published,
            'published_at' => $this->published ? now()->toDateTimeString() : null,
            'created_at' => now()->toDateTimeString(),
            'user_id' => $this->userId,
        ];
    }
}
