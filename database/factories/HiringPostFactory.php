<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HiringPostFactory extends Factory
{
    public function definition(): array
    {
        $title  = fake()->jobTitle();
        $status = fake()->randomElement(['draft', 'published', 'published', 'published']);

        return [
            'title'                => $title,
            'slug'                 => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 100000),
            'description'          => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
            'qualification'        => fake()->paragraph(2),
            'experience'           => fake()->randomElement(['0-1 years', '1-3 years', '3-5 years', '5+ years']),
            'department'           => fake()->randomElement(['Engineering', 'Marketing', 'Sales', 'Design', 'Support']),
            'location'             => fake()->city(),
            'work_type'            => fake()->randomElement(['onsite', 'remote', 'hybrid']),
            'employment_type'      => fake()->randomElement(['full-time', 'part-time', 'contract', 'internship']),
            'apply_url'            => fake()->url(),
            'status'               => $status,
            'author_id'            => User::factory(),
            'is_featured'          => fake()->boolean(20),
            'views_count'          => fake()->numberBetween(0, 5000),
            'application_deadline' => fake()->dateTimeBetween('now', '+2 months'),
            'published_at'         => $status === 'published' ? fake()->dateTimeBetween('-1 month') : null,
            'meta_title'           => $title,
            'meta_description'    => fake()->sentence(15),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }
}
