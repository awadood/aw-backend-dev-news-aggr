<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_retrieve_personalized_feed(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Create preferences for the user
        Preference::factory()->create(['user_id' => $user->id, 'name' => 'category', 'value' => 'technology']);
        Preference::factory()->create(['user_id' => $user->id, 'name' => 'source', 'value' => 'TechCrunch']);

        // Create articles matching preferences
        $article = Article::factory()->create(['title' => 'Tech Article', 'url' => 'https://tech.com/article1']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'category', 'value' => 'technology']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'source', 'value' => 'TechCrunch']);

        $this->actingAs($user, 'sanctum')
            ->getJson(route(RouteNames::ARTICLE_FEED))
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Tech Article']);
    }

    #[Test]
    public function can_retrieve_empty_feed_if_no_matching_articles(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Create preferences for the user
        Preference::factory()->create(['user_id' => $user->id, 'name' => 'category', 'value' => 'sports']);

        // Create articles not matching preferences
        $article = Article::factory()->create(['title' => 'Political News', 'url' => 'https://news.com/article1']);
        Attribute::factory()->create(['article_id' => $article->id, 'name' => 'category', 'value' => 'politics']);

        $this->actingAs($user, 'sanctum')
            ->getJson(route(RouteNames::ARTICLE_FEED))
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    #[Test]
    public function cannot_retrieve_feed_when_not_authenticated(): void
    {
        $this->getJson(route(RouteNames::ARTICLE_FEED))
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
