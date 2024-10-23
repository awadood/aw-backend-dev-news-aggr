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
    public function user_can_view_their_preferences()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $preference = Preference::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route(RouteNames::PREF_SHOW));

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'categories' => $preference->categories,
                'sources' => $preference->sources,
                'authors' => $preference->authors,
            ]);
    }

    #[Test]
    public function user_can_update_their_preferences()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $newPreferences = [
            'categories' => 'Technology, Health',
            'sources' => 'BBC, CNN',
            'authors' => 'John Doe, Jane Smith',
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')->postJson(route(RouteNames::PREF_STORE), $newPreferences);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment($newPreferences);

        $this->assertDatabaseHas('preferences', array_merge($newPreferences, ['user_id' => $user->id]));
    }

    #[Test]
    public function user_can_delete_their_preferences()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $preference = Preference::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/preferences');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => __('aggregator.preference.deleted')]);

        $this->assertDatabaseMissing('preferences', ['id' => $preference->id]);
    }
}
