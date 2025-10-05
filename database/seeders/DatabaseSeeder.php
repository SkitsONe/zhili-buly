<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Создаем пользователей
        $user1 = User::create([
            'name' => 'Иван Сергеевич',
            'email' => 'ivan@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Сергей Ивановчи',
            'email' => 'serg@example.com',
            'password' => Hash::make('password'),
        ]);

        // Создаем категории
        $category1 = Category::create(['name' => 'Технология', 'slug' => 'technology']);
        $category2 = Category::create(['name' => 'Путешествия', 'slug' => 'travel']);
        $category3 = Category::create(['name' => 'Еда', 'slug' => 'food']);

        // Создаем статьи
        Post::create([
            'title' => 'Первый блог',
            'slug' => 'first-blog-post',
            'content' => 'Это первый тестовый блог',
            'excerpt' => 'Краткое описание',
            'user_id' => $user1->id,
            'category_id' => $category1->id,
            'published' => true,
            'published_at' => now(),
        ]);

        Post::create([
            'title' => 'Невероятное путешествие',
            'slug' => 'amazing-travel-experience',
            'content' => 'Рассказ о первом путешествии...',
            'excerpt' => 'история путешествия',
            'user_id' => $user2->id,
            'category_id' => $category2->id,
            'published' => true,
            'published_at' => now(),
        ]);
    }
}
