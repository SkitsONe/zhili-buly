<?php

namespace App\Dto;

use Illuminate\Support\Str;

class CategoryDto
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $slug = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            slug: $data['slug'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug ?? Str::slug($this->name),
        ]);
    }
}
