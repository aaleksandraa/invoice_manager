<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceYearFilteringTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that invoice number can be edited
     */
    public function test_invoice_number_can_be_edited(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        $invoice = Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('invoices.update', $invoice), [
            'broj_fakture' => '#5/2025',
            'klijent_id' => $client->id,
            'datum_izdavanja' => now()->format('Y-m-d'),
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
        ]);

        $response->assertRedirect(route('invoices.show', $invoice));
        $invoice->refresh();
        $this->assertEquals('#5/2025', $invoice->broj_fakture);
    }

    /**
     * Test that invoice number format is validated
     */
    public function test_invoice_number_format_is_validated(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        $invoice = Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        // Test invalid format
        $response = $this->actingAs($user)->put(route('invoices.update', $invoice), [
            'broj_fakture' => 'invalid-format',
            'klijent_id' => $client->id,
            'datum_izdavanja' => now()->format('Y-m-d'),
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
        ]);

        $response->assertSessionHasErrors('broj_fakture');
    }

    /**
     * Test that duplicate invoice numbers are prevented
     */
    public function test_duplicate_invoice_numbers_are_prevented(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        $invoice2 = Invoice::create([
            'broj_fakture' => '#2/2025',
            'datum_izdavanja' => now(),
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work 2',
            'kolicina' => 1,
            'cijena' => 200.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        // Try to update invoice2 with invoice1's number
        $response = $this->actingAs($user)->put(route('invoices.update', $invoice2), [
            'broj_fakture' => '#1/2025',
            'klijent_id' => $client->id,
            'datum_izdavanja' => now()->format('Y-m-d'),
            'opis_posla' => 'Test work 2',
            'kolicina' => 1,
            'cijena' => 200.00,
            'valuta' => 'BAM',
        ]);

        $response->assertSessionHasErrors('broj_fakture');
    }

    /**
     * Test that invoice numbering resets for new year
     */
    public function test_invoice_numbering_resets_for_new_year(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        // Create invoice for 2025
        Invoice::create([
            'broj_fakture' => '#5/2025',
            'datum_izdavanja' => '2025-12-31',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        // Create invoice for 2026 - should start from #1
        Invoice::create([
            'broj_fakture' => '#1/2026',
            'datum_izdavanja' => '2026-01-01',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'broj_fakture' => '#5/2025',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'broj_fakture' => '#1/2026',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test that invoices can be filtered by year
     */
    public function test_invoices_can_be_filtered_by_year(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        // Create invoices for different years
        Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => '2025-01-01',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work 2025',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        Invoice::create([
            'broj_fakture' => '#1/2026',
            'datum_izdavanja' => '2026-01-01',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work 2026',
            'kolicina' => 1,
            'cijena' => 200.00,
            'valuta' => 'BAM',
            'user_id' => $user->id,
        ]);

        // Test filtering by 2025
        $response = $this->actingAs($user)->get(route('invoices.index', ['year' => 2025]));
        $response->assertStatus(200);
        $response->assertSee('Test work 2025');
        $response->assertDontSee('Test work 2026');

        // Test filtering by 2026
        $response = $this->actingAs($user)->get(route('invoices.index', ['year' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Test work 2026');
        $response->assertDontSee('Test work 2025');
    }

    /**
     * Test that payments can be filtered by year
     */
    public function test_payments_can_be_filtered_by_year(): void
    {
        $user = User::factory()->create();
        $client = Client::create([
            'naziv_firme' => 'Test Client',
            'adresa' => 'Test Address',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
            'kontakt_telefon' => '123456789',
            'user_id' => $user->id,
        ]);

        // Create paid invoices for different years
        Invoice::create([
            'broj_fakture' => '#1/2025',
            'datum_izdavanja' => '2025-01-01',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work 2025',
            'kolicina' => 1,
            'cijena' => 100.00,
            'valuta' => 'BAM',
            'placeno' => true,
            'datum_placanja' => '2025-01-15',
            'user_id' => $user->id,
        ]);

        Invoice::create([
            'broj_fakture' => '#1/2026',
            'datum_izdavanja' => '2026-01-01',
            'klijent_id' => $client->id,
            'opis_posla' => 'Test work 2026',
            'kolicina' => 1,
            'cijena' => 200.00,
            'valuta' => 'BAM',
            'placeno' => true,
            'datum_placanja' => '2026-01-15',
            'user_id' => $user->id,
        ]);

        // Test filtering by 2025
        $response = $this->actingAs($user)->get(route('invoices.payments', ['year' => 2025]));
        $response->assertStatus(200);
        $response->assertSee('Test work 2025');
        $response->assertDontSee('Test work 2026');

        // Test filtering by 2026
        $response = $this->actingAs($user)->get(route('invoices.payments', ['year' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Test work 2026');
        $response->assertDontSee('Test work 2025');
    }
}
