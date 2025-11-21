<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_edited()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Company',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456',
            'email' => 'test@example.com',
            'kontakt_telefon' => '+387 11 111 111',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('clients.update', $client), [
            'naziv_firme' => 'Updated Company',
            'adresa' => 'Updated Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456',
            'email' => 'updated@example.com',
            'kontakt_telefon' => '+387 22 222 222',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'naziv_firme' => 'Updated Company',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_client_can_be_deleted()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Company',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456',
            'email' => 'test@example.com',
            'kontakt_telefon' => '+387 11 111 111',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

        $response->assertStatus(302);
        $response->assertRedirect(route('clients.index'));

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_user_cannot_edit_another_users_client()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Company',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456',
            'email' => 'test@example.com',
            'kontakt_telefon' => '+387 11 111 111',
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user2)->put(route('clients.update', $client), [
            'naziv_firme' => 'Updated Company',
            'adresa' => 'Updated Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456',
            'email' => 'updated@example.com',
            'kontakt_telefon' => '+387 22 222 222',
        ]);

        $response->assertStatus(403);
    }
}
