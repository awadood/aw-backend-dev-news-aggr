<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_register()
    {
        $response = $this->postJson(route(RouteNames::AUTH_REGISTER), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    #[Test]
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson(route(RouteNames::AUTH_REGISTER), [
            'name' => '', // Empty name
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Password too short
            'password_confirmation' => 'different_password', // Does not match
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function a_user_can_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson(route(RouteNames::AUTH_LOGIN), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson(route(RouteNames::AUTH_LOGIN), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[Test]
    public function login_fails_with_missing_fields()
    {
        $response = $this->postJson(route(RouteNames::AUTH_LOGIN), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function a_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->postJson(route(RouteNames::AUTH_LOGOUT));

        $response->assertStatus(200)
            ->assertJson(['message' => __('auth.logged_out')]);
    }

    #[Test]
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson(route(RouteNames::AUTH_LOGOUT));

        $response->assertStatus(401); // Unauthorized
    }

    #[Test]
    public function a_user_can_request_password_reset_link()
    {
        $user = User::factory()->create();

        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => $user->email])
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->postJson(route(RouteNames::PASSWORD_RESET), ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'We have emailed your password reset link.']);
    }

    #[Test]
    public function password_reset_link_request_fails_for_nonexistent_email()
    {
        $response = $this->postJson(route(RouteNames::PASSWORD_RESET), ['email' => 'nonexistent@example.com']);

        $response->assertStatus(400) // Bad request
            ->assertJson(['message' => __('passwords.user')]);
    }

    #[Test]
    public function password_reset_link_request_fails_with_invalid_email()
    {
        $response = $this->postJson(route(RouteNames::PASSWORD_RESET), ['email' => 'invalid-email']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}
