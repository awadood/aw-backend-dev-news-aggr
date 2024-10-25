<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_all_articles()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        Article::factory()->count(5)->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route(RouteNames::ARTICLE_INDEX));

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function user_can_view_a_single_article()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/articles/'.$article->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => $article->title,
                'content' => $article->content,
            ]);
    }

    /** @test */
    public function user_can_create_an_article()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $articleData = [
            'title' => 'New Article Title',
            'content' => 'This is the content of the new article.',
            'category' => 'Technology',
            'source' => 'BBC',
            'author' => 'John Doe',
            'published_at' => now()->toDateTimeString(),
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/articles', $articleData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonFragment($articleData);

        $this->assertDatabaseHas('articles', $articleData);
    }

    /** @test */
    public function user_can_update_an_article()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $updatedData = [
            'title' => 'Updated Article Title',
            'content' => 'This is the updated content of the article.',
            'category' => 'Health',
            'source' => 'CNN',
            'author' => 'Jane Doe',
            'published_at' => now()->toDateTimeString(),
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/articles/'.$article->id, $updatedData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);

        $this->assertDatabaseHas('articles', $updatedData);
    }

    /** @test */
    public function user_can_delete_an_article()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/articles/'.$article->id);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Article deleted successfully']);

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /** @test */
    public function user_can_get_personalized_feed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $preferences = [
            'categories' => 'Technology, Health',
            'sources' => 'BBC, CNN',
            'authors' => 'John Doe, Jane Smith',
        ];
        $user->preference()->create($preferences);

        Article::factory()->create(['user_id' => $user->id, 'category' => 'Technology', 'source' => 'BBC', 'author' => 'John Doe']);
        Article::factory()->create(['user_id' => $user->id, 'category' => 'Health', 'source' => 'CNN', 'author' => 'Jane Smith']);
        Article::factory()->create(['user_id' => $user->id, 'category' => 'Politics', 'source' => 'Fox News', 'author' => 'Unrelated Author']);

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/personalized-feed');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
