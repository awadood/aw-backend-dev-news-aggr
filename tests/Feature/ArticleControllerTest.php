<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_fetch_paginated_articles(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        Article::factory()->count(15)->create();

        $response = $this->getJson(route(RouteNames::ARTICLE_INDEX).'?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data',
                'links',
                'per_page',
                'total',
            ])
            ->assertJsonCount(10, 'data'); // Default pagination per page is 10
    }

    #[Test]
    public function can_filter_articles_by_keyword(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $article = Article::factory()->create(['title' => 'Technology Today']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'keyword', 'value' => 'technology']);

        $response = $this->getJson(route(RouteNames::ARTICLE_INDEX).'?keyword=technology');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Technology Today']);
    }

    #[Test]
    public function can_filter_articles_by_date(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $article = Article::factory()->create(['title' => 'Tech News']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'date', 'value' => '2024-10-27 12:13:15']);

        $response = $this->getJson(route(RouteNames::ARTICLE_INDEX).'?date=2024-10-27');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Tech News']);
    }

    #[Test]
    public function can_filter_articles_by_category(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $article = Article::factory()->create(['title' => 'Business News']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'category', 'value' => 'business']);

        $response = $this->getJson(route(RouteNames::ARTICLE_INDEX).'?category=business');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Business News']);
    }

    #[Test]
    public function can_filter_articles_by_source(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $article = Article::factory()->create(['title' => 'News from CNN']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'source', 'value' => 'CNN']);

        $response = $this->getJson(route(RouteNames::ARTICLE_INDEX).'?source=CNN');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'News from CNN']);
    }

    #[Test]
    public function can_fetch_single_article(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $article = Article::factory()->create(['title' => 'Breaking News']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'category', 'value' => 'general']);

        $response = $this->getJson(route(RouteNames::ARTICLE_SHOW, $article->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Breaking News']);
    }

    #[Test]
    public function cannot_fetch_non_existent_article(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson(route(RouteNames::ARTICLE_SHOW, '999'));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Article not found.']);
    }
}
