<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_list_published_posts(): void
    {
        Post::factory()->published()->count(3)->create();
        Post::factory()->draft()->count(2)->create();

        $response = $this->getJson('/api/posts');

        $response->assertOk()
                 ->assertJsonStructure(['success', 'data', 'meta'])
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function guest_can_get_single_post_by_slug(): void
    {
        $post = Post::factory()->published()->create();

        $this->getJson("/api/posts/{$post->slug}")
             ->assertOk()
             ->assertJsonPath('data.slug', $post->slug);
    }

    /** @test */
    public function user_can_register_and_get_token(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(201)
          ->assertJsonStructure(['data' => ['token']]);
    }

    /** @test */
    public function user_can_login(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertOk()
          ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    /** @test */
    public function authenticated_user_can_create_post(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user, 'sanctum')
             ->postJson('/api/posts', [
                 'title'   => 'Test Post Title',
                 'content' => '<p>Test content here</p>',
                 'status'  => 'published',
                 'category_id' => $category->id,
             ])
             ->assertStatus(201)
             ->assertJsonPath('data.title', 'Test Post Title');
    }

    /** @test */
    public function guest_cannot_create_post(): void
    {
        $this->postJson('/api/posts', [
            'title'   => 'Unauthorized Post',
            'content' => 'Content',
        ])->assertStatus(401);
    }

    /** @test */
    public function can_filter_posts_by_category(): void
    {
        $tech    = Category::factory()->create(['slug' => 'tech']);
        $health  = Category::factory()->create(['slug' => 'health']);

        Post::factory()->published()->count(3)->create(['category_id' => $tech->id]);
        Post::factory()->published()->count(2)->create(['category_id' => $health->id]);

        $this->getJson('/api/posts?category=tech')
             ->assertOk()
             ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_list_categories(): void
    {
        Category::factory()->count(4)->create();

        $this->getJson('/api/categories')
             ->assertOk()
             ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function can_list_tags(): void
    {
        Tag::factory()->count(5)->create();

        $this->getJson('/api/tags')
             ->assertOk()
             ->assertJsonCount(5, 'data');
    }
}
