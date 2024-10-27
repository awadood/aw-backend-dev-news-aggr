<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\Preference;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
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
        $this->assertEquals(2, $user->preferences->count());
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

    #[Test]
    public function it_handles_exception_when_storing_preferences(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        DB::shouldReceive('beginTransaction')->andReturnNull();
        DB::shouldReceive('commit')->once()->andThrow(new Exception('mocked exception'));
        DB::shouldReceive('rollBack')->andReturnNull();

        // Mock the Log facade to assert the exception is logged
        Log::shouldReceive('error')->once()->with(Mockery::any());

        // Attempt to store preferences, which will trigger an exception
        $response = $this->postJson(route(RouteNames::PREF_STORE), [
            'preferences' => [
                ['name' => 'category', 'value' => 'technology'],
                ['name' => 'source', 'value' => 'TechCrunch'],
            ],
        ]);

        // Assert response is correct
        $response->assertStatus(500)
            ->assertJson(['error' => __('aggregator.preference.failed')]);
    }

    #[Test]
    public function belongs_to_a_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        $preference = Preference::factory()->create([
            'user_id' => $user->id,
            'name' => 'category',
            'value' => 'technology',
        ]);

        // Step 3: Assert that the preference belongs to the correct user
        $this->assertInstanceOf(User::class, $preference->user);
        $this->assertEquals($user->id, $preference->user->id);
    }
}
