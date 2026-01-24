<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisteredUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_can_register_and_get_token()
    {
        Notification::fake();

        $userEmail = $this->faker->unique()->safeEmail();
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => $userEmail,
            'password' => 'password123',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'created',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userEmail,
        ]);

        Notification::assertSentTo(
            User::latest('id')->first(),
            VerifyEmail::class
        );
    }
}
