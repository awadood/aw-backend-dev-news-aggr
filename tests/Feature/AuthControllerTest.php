<?php

namespace Tests\Feature;

use App\Constants\RouteNames;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function a_user_can_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson(route(RouteNames::AUTH_LOGIN), ['email' => $user->email, 'password' => 'password']);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    #[Test]
    public function a_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson(route(RouteNames::AUTH_LOGIN), ['email' => $user->email, 'password' => 'somepassword']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[Test]
    public function a_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->postJson(route(RouteNames::AUTH_LOGOUT));

        dump(__('auth.logged_out'));
        $response->assertStatus(200)
            ->assertJson(['message' => __('auth.logged_out')]);
    }

    #[Test]
    public function a_user_can_request_password_reset_link()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route(RouteNames::PASSWORD_RESET), ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'We have emailed your password reset link.']);
    }
}
