<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ───────────────────────────────────────────
        $admin = User::create([
            'name'      => 'Admin User',
            'email'     => 'admin@blog.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Editor User',
            'email'     => 'editor@blog.com',
            'password'  => Hash::make('password'),
            'role'      => 'editor',
            'is_active' => true,
        ]);

        // ── Categories ───────────────────────────────────────────
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest tech news and tutorials'],
            ['name' => 'Business',   'slug' => 'business',   'description' => 'Business insights and tips'],
            ['name' => 'Health',     'slug' => 'health',     'description' => 'Health and wellness articles'],
            ['name' => 'Travel',     'slug' => 'travel',     'description' => 'Travel guides and experiences'],
            ['name' => 'Food',       'slug' => 'food',       'description' => 'Recipes and food reviews'],
        ];

        foreach ($categories as $i => $catData) {
            Category::create([...$catData, 'is_active' => true, 'sort_order' => $i]);
        }

        // ── Tags ─────────────────────────────────────────────────
        $tagNames = ['Laravel', 'PHP', 'JavaScript', 'React', 'Vue', 'API', 'Tips', 'Tutorial', 'News', 'Review'];
        $tags = [];
        foreach ($tagNames as $name) {
            $tags[] = Tag::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        // ── Sample Posts ─────────────────────────────────────────
        $samplePosts = [
            [
                'title'   => 'Getting Started with Laravel 10',
                'excerpt' => 'A comprehensive guide to building modern web applications with Laravel 10.',
                'content' => '<h2>Introduction</h2><p>Laravel is a web application framework with expressive, elegant syntax. It provides the structure and starting point for creating your application.</p><h2>Installation</h2><p>Install via Composer: <code>composer create-project laravel/laravel example-app</code></p><p>Laravel makes it easy to build modern web applications with features like Eloquent ORM, Blade templating, and built-in authentication.</p>',
                'status'  => 'published',
                'is_featured' => true,
            ],
            [
                'title'   => 'Building REST APIs with Laravel Sanctum',
                'excerpt' => 'Learn how to secure your Laravel APIs using Sanctum token authentication.',
                'content' => '<h2>What is Sanctum?</h2><p>Laravel Sanctum provides a featherweight authentication system for SPAs, mobile applications, and simple, token based APIs.</p><h2>Setup</h2><p>Run <code>php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"</code> to publish Sanctum\'s configuration and migration files.</p>',
                'status'  => 'published',
                'is_featured' => false,
            ],
            [
                'title'   => 'Top 10 PHP Tips for 2024',
                'excerpt' => 'Improve your PHP code quality with these modern best practices.',
                'content' => '<p>PHP has evolved dramatically. Here are 10 tips to write cleaner, faster PHP code in 2024.</p><ul><li>Use typed properties</li><li>Leverage match expressions</li><li>Use named arguments</li><li>Embrace fibers for async code</li></ul>',
                'status'  => 'draft',
                'is_featured' => false,
            ],
        ];

        $categoryIds = Category::pluck('id')->toArray();

        foreach ($samplePosts as $i => $postData) {
            $post = Post::create([
                ...$postData,
                'slug'         => Str::slug($postData['title']),
                'author_id'    => $admin->id,
                'category_id'  => $categoryIds[$i % count($categoryIds)],
                'views_count'  => rand(50, 2000),
                'published_at' => $postData['status'] === 'published' ? now()->subDays($i) : null,
                'allow_comments' => true,
                'meta_title'   => $postData['title'],
                'meta_description' => $postData['excerpt'],
            ]);

            // Attach 2-3 random tags
            $post->tags()->attach(
                collect($tags)->random(rand(2, 3))->pluck('id')->toArray()
            );
        }

        $this->command->info('✅ Database seeded!');
        $this->command->info('   Admin: admin@blog.com / password');
        $this->command->info('   Editor: editor@blog.com / password');
    }
}
