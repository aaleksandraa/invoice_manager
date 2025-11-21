<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_reminder_settings()
    {
        $user = User::factory()->create([
            'reminder_enabled' => false,
            'reminder_interval' => 5,
        ]);

        $response = $this->actingAs($user)->put(route('settings.update-reminders'), [
            'reminder_enabled' => true,
            'reminder_interval' => 10,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'reminder_enabled' => true,
            'reminder_interval' => 10,
        ]);
    }

    public function test_reminder_interval_validation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('settings.update-reminders'), [
            'reminder_enabled' => true,
            'reminder_interval' => 7, // Invalid value
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('reminder_interval');
    }

    public function test_reminder_settings_defaults()
    {
        $user = User::factory()->create();

        $this->assertTrue($user->reminder_enabled);
        $this->assertEquals(5, $user->reminder_interval);
    }
}
