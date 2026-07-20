<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);
        $status = fake()->randomElement(['draft', 'published', 'published', 'published']);

        return [
            'title'            => $title,
            'slug'             => Str::slug($title),
            'excerpt'          => fake()->paragraph(2),
            'content'          => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
            'status'           => $status,
            'author_id'        => User::factory(),
            'category_id'      => Category::factory(),
            'is_featured'      => fake()->boolean(20),
            'allow_comments'   => true,
            'views_count'      => fake()->numberBetween(0, 5000),
            'published_at'     => $status === 'published' ? fake()->dateTimeBetween('-1 year') : null,
            'meta_title'       => $title,
            'meta_description' => fake()->sentence(15),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }
}
