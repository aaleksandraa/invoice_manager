<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an invoice can be marked as paid
     */
    public function test_invoice_can_be_marked_as_paid(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a client for this user
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        // Create an unpaid invoice
        $invoice = Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'placeno' => false,
            'user_id' => $user->id,
        ]);

        $this->assertFalse($invoice->placeno);

        // Update payment status
        $response = $this->actingAs($user)->put(route('invoices.update-payment-status', $invoice), [
            'placeno' => true,
            'datum_placanja' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('invoices.show', $invoice));
        $response->assertSessionHas('success');

        // Verify invoice is now paid
        $invoice->refresh();
        $this->assertTrue($invoice->placeno);
        $this->assertNotNull($invoice->datum_placanja);
    }

    /**
     * Test that an invoice can be marked as unpaid
     */
    public function test_invoice_can_be_marked_as_unpaid(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a client for this user
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        // Create a paid invoice
        $invoice = Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'placeno' => true,
            'datum_placanja' => now(),
            'user_id' => $user->id,
        ]);

        $this->assertTrue($invoice->placeno);

        // Update payment status to unpaid (checkbox not sent)
        $response = $this->actingAs($user)->put(route('invoices.update-payment-status', $invoice), [
            'datum_placanja' => '',
        ]);

        $response->assertRedirect(route('invoices.show', $invoice));
        $response->assertSessionHas('success');

        // Verify invoice is now unpaid
        $invoice->refresh();
        $this->assertFalse($invoice->placeno);
        $this->assertNull($invoice->datum_placanja);
    }
}
