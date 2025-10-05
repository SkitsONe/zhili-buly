<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepository::class);
        $this->app->bind(CategoryRepository::class);

        $this->app->bind(PostService::class, function ($app) {
            return new PostService($app->make(PostRepository::class));
        });

        $this->app->bind(CategoryService::class);
    }

    public function boot(): void
    {
        //
    }
}
