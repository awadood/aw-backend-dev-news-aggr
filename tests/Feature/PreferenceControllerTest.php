<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_retrieve_user_preferences(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Preference::factory()->create(['user_id' => $user->id, 'name' => 'category', 'value' => 'technology']);
        Preference::factory()->create(['user_id' => $user->id, 'name' => 'source', 'value' => 'TechCrunch']);

        $this->actingAs($user, 'sanctum')
            ->getJson(route(RouteNames::PREF_SHOW))
            ->assertStatus(200)
            ->assertJson([['name' => 'category', 'value' => 'technology'], ['name' => 'source', 'value' => 'TechCrunch']]);
    }

    #[Test]
    public function can_store_user_preferences(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $preferences = [
            ['name' => 'category', 'value' => 'business'],
            ['name' => 'source', 'value' => 'BBC News'],
        ];

        $this->actingAs($user, 'sanctum')
            ->postJson(route(RouteNames::PREF_STORE), ['preferences' => $preferences])
            ->assertStatus(200)
            ->assertJson(['message' => __('aggregator.preference.stored')]);

        $this->assertDatabaseHas('preferences', [
            'user_id' => $user->id,
            'name' => 'category',
            'value' => 'business',
        ]);

        $this->assertDatabaseHas('preferences', [
            'user_id' => $user->id,
            'name' => 'source',
            'value' => 'BBC News',
        ]);
    }

    #[Test]
    public function cannot_retrieve_preferences_when_not_authenticated(): void
    {
        $this->getJson(route(RouteNames::PREF_SHOW))
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function cannot_store_preferences_when_not_authenticated(): void
    {
        $preferences = [
            ['name' => 'category', 'value' => 'business'],
            ['name' => 'source', 'value' => 'BBC News'],
        ];

        $this->postJson(route(RouteNames::PREF_SHOW), ['preferences' => $preferences])
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
