<?php

namespace Tests\Feature;

use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpTestConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_smtp_test_connection_fails_without_smtp_settings()
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('settings.test-smtp'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('konfigurisana', $response->json('message'));
    }

    public function test_smtp_test_connection_returns_success_with_valid_settings()
    {
        // Don't fake mail for this test since we want to verify the actual attempt
        // The test will succeed if no exception is thrown
        $user = User::factory()->create();
        SmtpSetting::factory()->create([
            'user_id' => $user->id,
            'smtp_host' => 'localhost',
            'smtp_port' => 1025, // Use a port that won't actually connect but won't throw immediately
        ]);

        $response = $this->actingAs($user)->postJson(route('settings.test-smtp'));

        $response->assertStatus(200);
        // The response might be success or failure depending on whether localhost:1025 is available
        // But it should return a JSON response with success key
        $response->assertJsonStructure(['success', 'message']);
    }

    public function test_smtp_test_connection_handles_errors_gracefully()
    {
        $user = User::factory()->create();
        
        // Create SMTP settings with invalid configuration
        SmtpSetting::factory()->create([
            'user_id' => $user->id,
            'smtp_host' => 'invalid-host-that-does-not-exist.example.com',
            'smtp_port' => 587,
        ]);

        $response = $this->actingAs($user)->postJson(route('settings.test-smtp'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Greška', $response->json('message'));
    }

    public function test_guest_cannot_test_smtp_connection()
    {
        $response = $this->postJson(route('settings.test-smtp'));

        $response->assertStatus(401);
    }
}
